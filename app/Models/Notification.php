<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'channel', // القناة: sms, push, email
        'target_users', // المستهدمين: الكل، المستأجرين، المؤجرين، مستخدم محدد
        'meta',
        'read_at',
        'sent_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get the display name for channel
    public function getChannelNameAttribute()
    {
        return match ($this->channel) {
            'sms' => 'SMS',
            'email' => 'Email',
            'app' => 'داخل التطبيق',
            'push' => 'داخل التطبيق',
            default => 'غير محدد',
        };
    }

    // Get the display name for target users
    public function getTargetUsersNameAttribute()
    {
        return match ($this->target_users) {
            'all' => 'الكل',
            'tenants' => 'المستأجرين',
            'landlords' => 'المؤجرين',
            'specific' => 'مستخدم محدد',
            default => 'غير محدد',
        };
    }

    // Get the display name for type
    public function getTypeNameAttribute()
    {
        return match ($this->type) {
            'alert' => 'تنبيه',
            'booking_confirm' => 'تأكيد حجز',
            'booking_completed' => 'اكتمال حجز',
            'booking_new_request' => 'طلب حجز جديد',
            'booking_cancelled' => 'إلغاء حجز',
            'announcement' => 'تنبيه',
            'info' => 'تأكيد حجز',
            default => 'إشعار',
        };
    }
}
