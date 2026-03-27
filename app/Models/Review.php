<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'reviewed_user_id',
        'reviewer_user_id',
        'booking_id',
        'rating',
        'property_care',
        'cleanliness',
        'rules_compliance',
        'timely_delivery',
        'inquiry_response',
        'booking_acceptance_speed',
        'comment',
        'start_date',
        'end_date',
    ];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_user_id');
    }

    public function reviewedUser()
    {
        return $this->belongsTo(User::class, 'reviewed_user_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
