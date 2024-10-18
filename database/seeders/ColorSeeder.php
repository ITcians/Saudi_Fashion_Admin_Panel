<?php

namespace Database\Seeders;

use App\Models\ColorModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ColorModel::create([
            'color_name' => "Light Pink",
            'color_code' => '#FFB6C1',
            'product_id' => 1,
         ]);
         ColorModel::create([
            'color_name' => "Dark Red",
            'color_code' => '#8B0000',
            'product_id' => 1,
         ]);
    }
}
