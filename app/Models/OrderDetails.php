<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $table = 'order_details';
    protected $fillable = [
        'product_id',
    'order_id',
        'customer_id' ,
        'designer_id',
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
