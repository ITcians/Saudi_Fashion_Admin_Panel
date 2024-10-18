<?php

namespace Database\Seeders;

use App\Models\UserModels\UserType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserType::create([
            'type'=>"Designer",
            'type_description'=>"Showcase your creativity and connect with fashion enthusiasts around the world.",
            'icon'=>'/images/designer_icon.png'
        ]);
        UserType::create([
            'type'=>"Customer",
            'type_description'=>"Discovering the latest trends, supporting independent designers, and expressing your unique style",
            'icon'=>'/images/customer_icon.png'
        ]);
    }
}
