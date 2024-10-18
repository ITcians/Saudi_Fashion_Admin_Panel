<?php

namespace Database\Seeders;

use App\Models\FlagPostModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FlagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FlagPostModel::create([
            'post_id' => 1,
            'flagged_by_user_id' => 1,
            'reason' => "This content is illegal"
        ]);
    }
}
