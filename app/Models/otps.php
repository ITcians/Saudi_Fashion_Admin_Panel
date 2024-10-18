<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class otps extends Model
{
    use HasFactory;

    protected $fillable=[
        'otp',
        'is_expired',
        'is_used',
        'email',
    ];
}
