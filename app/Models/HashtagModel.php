<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HashtagModel extends Model
{
    use HasFactory;

    protected $table = 'hashtags';

    protected $fillable = [
        'hashtag',
        'post_id',
    ];


}
