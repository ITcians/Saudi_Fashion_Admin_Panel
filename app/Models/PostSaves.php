<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostSaves extends Model
{
    use HasFactory;

    protected $table = 'post_saves';

    protected $fillable = [
        'post_id',
        'user_id',
    ];
}
