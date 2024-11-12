<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    use HasFactory;
 
    protected $table = 'orders';
    protected $fillable = [
        'customer_id' ,
        'desginer_id',
        'invoice_id',
        'total_amount',
        'status',
    ];
    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class, 'order_id');
    }

    public function customer() 
    {
        return $this->belongsTo(User::class,'customer_id');
    }
    public function desginer() 
    {
        return $this->belongsTo(User::class,'desginer_id');
    }


}
