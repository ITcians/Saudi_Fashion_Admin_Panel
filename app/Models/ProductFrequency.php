<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFrequency extends Model
{
    use HasFactory;

    protected $table = 'product_frequencies';

    protected $fillable = [
        'product_id',
        'user_id',
        'created_by_id',
    ];
}
