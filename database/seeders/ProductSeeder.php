<?php

namespace Database\Seeders;

use App\Models\ProductModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductModel::create([
            'title' => "Jeans Pant",
            'description' => "Stylish black dress for formal events",
            'care_advice' => "Dry clean recommended, avoid harsh detergents.",
            'material' => "High-quality polyester blend fabric.",
            'price' => 1900,
            'quantity' => 50,
            'created_by' => 1,
            'created_by' => 1,
            'category_id' => 2,
            'sub_category_id' => 1,
        ]);

    }
}
