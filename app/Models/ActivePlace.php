<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivePlace extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_type', // landlord | tenant
        'name',
        'city',
        'area',
        'available_from',
        'booking_type', // e.g., إيجار يومي | إيجار ليلي
        'price',
        'price_unit', // د.ل / اليوم | د.ل / ليلة
        'rating',
        'image',
        'is_published',
    ];

    protected $casts = [
        'available_from' => 'date',
        'price' => 'decimal:2',
        'rating' => 'decimal:1',
        'is_published' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
