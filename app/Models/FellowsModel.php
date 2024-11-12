<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FellowsModel extends Model
{
    use HasFactory;

    protected $table = 'fellows';

    protected $fillable=[
        'following_user_id',
        'follower_user_id',
    ];

    public function getUserForFollowing()
    {
        return $this->belongsTo(User::class, 'following_user_id');
    }

    public function getUserForFollower()
    {
        return $this->belongsTo(User::class, 'follower_user_id');
    }

}
