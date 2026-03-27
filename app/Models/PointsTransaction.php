<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointsTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'points',
        'type',
        'reason',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
