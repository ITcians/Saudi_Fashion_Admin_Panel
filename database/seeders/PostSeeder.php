<?php

namespace Database\Seeders;

use App\Models\PostModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PostModel::create([
            'post' => "AI Post",
            'cover' => "/upload_images/1729076611.jpg",
            'allow_comments' => 1,
            'visibiliy' => 1,
            'is_drafted' => 1,
            'status' => 200,
            'created_by' => 1
        ]);
    }
}
