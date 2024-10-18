<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TapModel extends Model
{
    use HasFactory;

    protected $table = 'tap_models';

    protected $fillable = [
        'name',
        'email',
        'tran_id',
        'amount',
    ];
}
