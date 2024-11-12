<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferPromotion extends Model
{
    use HasFactory;

    protected $table = "offer_promotions";

    protected $fillable = [
        'id',
        'title',
        'description',
        'percentage',
        'product_id',
        'created_by',
        'status', // 403 offline , 200 online
    ];

    public function createdBy(){
        return $this->belongsTo(User::class ,'created_by');
    }

    public function product(){
        return $this->belongsTo(ProductModel::class,'product_id');
    }
}
