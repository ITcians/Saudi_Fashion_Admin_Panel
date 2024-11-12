<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSizeModel extends Model
{
    use HasFactory;

    protected $table = 'product_sizes';

    protected $fillable = [
        'size',
        'product_id',
    ];

}
