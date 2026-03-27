<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicketReply extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'admin_id',
        'message',
        'is_admin_reply',
        'attachments',
    ];

    protected $casts = [
        'is_admin_reply' => 'boolean',
        'attachments' => 'array',
    ];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
