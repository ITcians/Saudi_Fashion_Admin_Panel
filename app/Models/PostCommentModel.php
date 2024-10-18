<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostCommentModel extends Model
{
    use HasFactory;

    protected $table = "post_comments";

    protected $fillable = [
        'comment',
        'post_id',
        'user_id',
        'reply_to_user_id',
        'created_at'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function post(){
        return $this->belongsTo(PostModel::class, 'post_id');
    }

}
