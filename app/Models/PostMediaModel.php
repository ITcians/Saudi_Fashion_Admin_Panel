<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostMediaModel extends Model
{
    use HasFactory;


    protected $table = 'post_media';

    protected $fillable = [
        'media',
        'type',
        'post_id',
    ];

}
