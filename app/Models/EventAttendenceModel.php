<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAttendenceModel extends Model
{
    use HasFactory;

    protected $table = 'event_attendees';

    protected $fillable = [
        'user_id',
        'status',
        'event_id'
    ];
}
