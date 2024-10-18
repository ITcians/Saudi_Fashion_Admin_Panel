<?php

namespace Database\Seeders;

use App\Models\CategoryModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CategoryModel::create([
            'category'=>'Women',
            'icon'=>'/images/category-women.png'
        ]);
        CategoryModel::create([
            'category'=>'Men',
            'icon'=>'/images/category-men.png'
        ]);
        CategoryModel::create([
            'category'=>'Shoe',
            'icon'=>'/images/category-shoe.png'
        ]);
        CategoryModel::create([
            'category'=>'Bag',
            'icon'=>'/images/category-bag.png'
        ]);
        CategoryModel::create([
            'category'=>'Lifestyle',
            'icon'=>'/images/category-lifestyle.png'
        ]);
        CategoryModel::create([
            'category'=>'Sports',
            'icon'=>'/images/category-sports.png'
        ]);
    }
}
