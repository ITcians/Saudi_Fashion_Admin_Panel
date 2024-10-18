<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fullname',
        'email',
        'username',
        'password',
        'phone',
        'country_code',
        'account_status',
        'account_type',
        'image',

    ];

     // Define the relationship to posts
     public function posts()
     {
         return $this->hasMany(PostModel::class, 'created_by');
     }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    

    public function mIFollowing(string $id)
    {
        return FellowsModel::where([
            'following_user_id' => $id,
            'follower_user_id' => Auth::id()
        ])->exists();
    }
    


    public function events(){
        return $this->hasMany(EventsModel::class,'created_by');
    }
}
