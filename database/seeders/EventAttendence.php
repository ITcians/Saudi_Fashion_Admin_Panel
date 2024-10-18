<?php

namespace Database\Seeders;

use App\Models\EventAttendenceModel;
use App\Models\EventAttendences;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventAttendence extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventAttendenceModel::create([
           'user_id' => 1,
           'status' => 'Interested',
           'event_id' => 1,
        ]);
    }
}
