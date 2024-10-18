<?php

namespace Database\Seeders;

use App\Models\EventsModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventsModel::create([
            'event_name' => "Zain's Marriage",
            'event_date' => "2024-05-10",
            'event_description' => "I want to do a Marriage with  ?",
            'cover_image' => "upload_images/1722582314.png",
            'event_hour' => "23:12:00",
            'event_status' => 200,
            'created_by' => 1
        ]);
    }
}
