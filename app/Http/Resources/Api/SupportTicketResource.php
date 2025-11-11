<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportTicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'coopId' => $this->coopId,
            'subject' => $this->subject,
            'priority' => $this->priority,
            'status' => $this->status,
            'message' => $this->message,
            'attachments' => $this->attachments,
            'closed_by' => $this->closed_by,
            'closed_at' => $this->closed_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'member' => $this->whenLoaded('member'),
            'messages' => $this->whenLoaded('messages'),
        ];
    }
}
