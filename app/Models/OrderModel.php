<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'product_id',
        'customer_id' ,
        'address_id',
        'color_id',
        'size_id',
        'quantity',
        'invoice_id',
        'status',
    ];

    public function product() 
    {
        return $this->belongsTo(ProductModel::class,'product_id');
    }
}
