<?php

namespace App\Models\UserModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use HasFactory;
    protected $table='user_types';
    protected $fillable = [
        'type', 'type_description',
        'icon'
    ];
}
