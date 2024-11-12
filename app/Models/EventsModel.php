<?php

namespace App\Models;

use Database\Seeders\EventAttendence;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventsModel extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable=[
        'event_name',
        'event_date',
        'event_description',
        'cover_image',
        'event_hour',
        'event_status',
        'created_by'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attendies(){
        return $this->hasMany(EventAttendenceModel::class,'event_id');
    }

}
