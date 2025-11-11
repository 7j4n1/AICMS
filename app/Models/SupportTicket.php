<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'coopId',
        'subject',
        'priority',
        'status',
        'message',
        'attachments',
        'closed_by',
        'closed_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'closed_at' => 'datetime',
    ];

    /**
     * Get the member that owns the ticket
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'coopId', 'coopId');
    }

    /**
     * Get all messages for this ticket
     */
    public function messages()
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id');
    }

    /**
     * Get the admin who closed the ticket
     */
    public function closedBy()
    {
        return $this->belongsTo(Admin::class, 'closed_by');
    }

    /**
     * Scope a query to only include open tickets
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope a query to only include closed tickets
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }
}
