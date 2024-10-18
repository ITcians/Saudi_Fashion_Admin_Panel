<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMediaModel extends Model
{
    use HasFactory;
    protected $table = 'product_media';
    protected $fillable = [
        'media',
        'type',
        'product_id',
    ];

    function product(){
        return $this->belongsTo(ProductModel::class,'product_id');
    }
}
