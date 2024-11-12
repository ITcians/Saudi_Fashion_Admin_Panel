<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategoryModel extends Model
{
    use HasFactory;
    protected $table='sub_categories';
    protected $fillable=[
        'sub_category',
        'category_id'
    ];
}
