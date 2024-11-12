<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlagPostModel extends Model
{
    use HasFactory;

    protected $table = 'flag_post';

    protected $fillable = [
       'post_id',
       'flagged_by_user_id',
       'reason',
    ];

    public function post()
    {
        return $this->belongsTo(PostModel::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'flagged_by_user_id');
    }

}
