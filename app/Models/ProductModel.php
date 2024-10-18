<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable=[
        'title',
        'description',
        'care_advice',
        'material',
        'price',
        'quantity',
        'created_by',
        'category_id',
        'sub_category_id',
        'status'

    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    function media(){
        return $this->hasMany(ProductMediaModel::class,'product_id');
    }
    function colors(){
        return $this->hasMany(ColorModel::class,'product_id');
    }
    function sizes(){
        return $this->hasMany(ProductSizeModel::class,'product_id');
    }
    function category(){
        return $this->belongsTo(CategoryModel::class,'category_id');
    }
    function sub_category(){
        return $this->belongsTo(SubCategoryModel::class,'sub_category_id');
    }

}
