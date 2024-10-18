<?php

namespace App\Models;

use Database\Seeders\SubCategories;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
    use HasFactory;
    protected $table='categories';
    protected $fillable=[
        'category',
        'description',
        'icon'
    ];

    function sub_categories(){
        return $this->hasMany(SubCategoryModel::class,'category_id');
    }
}
