<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddToCart extends Model
{
    use HasFactory;

    protected $table = 'add_to_carts';

    protected $fillable = [
        'customer_id',
        'product_id',
        'color_id',
        'size_id',
        'quantity',
    ];

    public function Product()
    {
        return $this->belongsTo(ProductModel::class,'product_id');
    }
    public function Color()
    {
        return $this->belongsTo(ColorModel::class,'color_id');
    }
    public function Size()
    {
        return $this->belongsTo(ProductSizeModel::class,'size_id');
    }

    function Media(){
        return $this->belongsTo(ProductMediaModel::class,'product_id');
    }
}   
