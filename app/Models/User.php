<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'user_type',
        'id_verified',
        'face_verified',
        'is_influencer',
        'needs_renewal',
        'status',
        'job',
        'city',
        'has_pet',
        'rating',
        'reviews_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // علاقات أساسية
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function properties()
    {
        // ترتبط العقارات بالمستخدم عبر عمود user_id في جدول properties
        return $this->hasMany(Property::class, 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // المراجعات التي تلقاها المستخدم
    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewed_user_id');
    }

    // الحجوزات السابقة (مكتملة)
    public function previousBookings()
    {
        return $this->hasMany(Booking::class)->where('status', 'completed');
    }

    public function favoriteProperties()
    {
        return $this->belongsToMany(Property::class, 'favorites', 'user_id', 'property_id')->withTimestamps();
    }
}
