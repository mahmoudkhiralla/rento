<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuspendedUser extends Model
{
    use HasFactory;

    protected $table = 'suspended_users';

    protected $fillable = [
        'user_id',
        'admin_id',
        'reason',
        'duration',
        'ends_at',
        'released_at',
        'status',
    ];

    protected $casts = [
        'ends_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
