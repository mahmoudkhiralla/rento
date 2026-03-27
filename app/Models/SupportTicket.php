<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'description',
        'status', // 'open', 'in_progress', 'resolved', 'closed'
        'priority', // 'low', 'medium', 'high', 'urgent'
        'assigned_to',
        'category',
        'last_replied_at',
        'booking_id',
        'property_id',
        'landlord_id',
        'tenant_id',
        'submitted_by',
    ];

    protected $casts = [
        'last_replied_at' => 'datetime',
        'admin_read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(Admin::class, 'assigned_to');
    }

    public function replies()
    {
        return $this->hasMany(SupportTicketReply::class, 'ticket_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }

    public function scopeClosed($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }
}
