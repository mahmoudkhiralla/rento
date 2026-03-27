<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_type_id',
        'title',
        'city',
        'address',
        'rental_type',
        'capacity',
        'bedrooms',
        'bathrooms',
        'price',
        'description',
        'keywords',
        'approved',
        'status',
        // صورة رئيسية للعقار
        'image',
    ];

    protected $casts = [
        'keywords' => 'array',
        'price' => 'decimal:2',
        'approved' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'amenity_property');
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }
}
