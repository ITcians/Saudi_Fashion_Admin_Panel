<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlagCommmentModel extends Model
{
    use HasFactory;

    protected $table = 'flag_comments';

    protected $fillable = [
        'post_id',
        'comment_id',
        'flagged_by_user_id',
        'reason',
    ];
}
