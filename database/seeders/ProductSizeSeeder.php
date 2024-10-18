<?php

namespace Database\Seeders;

use App\Models\ProductSizeModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductSizeModel::create([
            'size' => "32",
            'product_id' => 1,
         ]);
    }
}
