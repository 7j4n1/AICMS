<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'sender_id',
        'sender_type',
        'message',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    /**
     * Get the ticket that owns the message
     */
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Get the sender (polymorphic relation)
     */
    public function sender()
    {
        if ($this->sender_type === 'admin') {
            return $this->belongsTo(Admin::class, 'sender_id');
        }
        return $this->belongsTo(Member::class, 'sender_id', 'coopId');
    }
}
