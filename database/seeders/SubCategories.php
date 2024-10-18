<?php

namespace Database\Seeders;

use App\Models\SubCategoryModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubCategories extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubCategoryModel::create([
            'sub_category' => "Mens New Collection",
            'category_id' => 1,
         ]);
    }
}
