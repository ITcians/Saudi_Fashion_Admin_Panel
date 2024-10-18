<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PostModel extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable=[
        'post',
        'cover',
        'allow_comments',
        'visibiliy',
        'is_drafted',
        'status',
        'created_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function flag()
    {
        return $this->hasOne(FlagPostModel::class);
    }

    public function createdBy()
{
    return $this->belongsTo(User::class, 'created_by');
}

}
