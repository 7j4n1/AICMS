<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of support tickets
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $status = $request->input('status');
        $priority = $request->input('priority');
        $user = $request->user();

        $query = SupportTicket::with(['member', 'messages']);

        // If user is a member (not admin), only show their tickets
        if ($user->coopId && !$user->role) {
            $query->where('coopId', $user->coopId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $tickets->items(),
            'meta' => [
                'current_page' => $tickets->currentPage(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
                'last_page' => $tickets->lastPage()
            ]
        ]);
    }

    /**
     * Store a newly created support ticket
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,pdf,doc,docx|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $attachmentPaths = [];

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('public/tickets');
                $attachmentPaths[] = $path;
            }
        }

        $ticket = SupportTicket::create([
            'coopId' => $user->coopId,
            'subject' => $request->subject,
            'priority' => $request->priority,
            'message' => $request->message,
            'attachments' => $attachmentPaths,
            'status' => 'open',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Support ticket created successfully',
            'data' => $ticket->load('member')
        ], 201);
    }

    /**
     * Display the specified support ticket
     */
    public function show(Request $request, string $id)
    {
        $ticket = SupportTicket::with(['member', 'messages'])->find($id);

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Support ticket not found'
            ], 404);
        }

        $user = $request->user();
        
        // Check if user has access to this ticket
        if ($user->coopId && !$user->role && $ticket->coopId != $user->coopId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $ticket
        ]);
    }

    /**
     * Update the specified support ticket (mainly for closing)
     */
    public function update(Request $request, string $id)
    {
        $ticket = SupportTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Support ticket not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:open,replied,closed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('status') && $request->status === 'closed') {
            $ticket->update([
                'status' => 'closed',
                'closed_by' => $request->user()->id,
                'closed_at' => now(),
            ]);
        } else {
            $ticket->update($validator->validated());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Support ticket updated successfully',
            'data' => $ticket->load('member')
        ]);
    }

    /**
     * Remove the specified support ticket
     */
    public function destroy(string $id)
    {
        $ticket = SupportTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Support ticket not found'
            ], 404);
        }

        // Delete attachments from storage
        if ($ticket->attachments) {
            foreach ($ticket->attachments as $path) {
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
            }
        }

        $ticket->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Support ticket deleted successfully'
        ]);
    }

    /**
     * Add a message/reply to a ticket
     */
    public function addMessage(Request $request, string $id)
    {
        $ticket = SupportTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Support ticket not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,pdf,doc,docx|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $attachmentPaths = [];

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('public/tickets');
                $attachmentPaths[] = $path;
            }
        }

        // Determine sender type
        $senderType = $user->role ? 'admin' : 'member';
        $senderId = $senderType === 'admin' ? $user->id : $user->coopId;

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $senderId,
            'sender_type' => $senderType,
            'message' => $request->message,
            'attachments' => $attachmentPaths,
        ]);

        // Update ticket status to 'replied' if admin is responding
        if ($senderType === 'admin') {
            $ticket->update(['status' => 'replied']);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Message added successfully',
            'data' => $message
        ], 201);
    }
}
