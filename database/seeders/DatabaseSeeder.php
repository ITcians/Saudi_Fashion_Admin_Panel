<?php

namespace Database\Seeders;

use App\Models\SettingModel;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            SettingSeeder::class,
            UserTypeSeeder::class,
            CategorySeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
            ProductSizeSeeder::class,
            EventSeeder::class,
            PostSeeder::class,
            FlagSeeder::class,
            EventAttendence::class,
            ColorSeeder::class,
            SubCategories::class,
            ColorSeeder::class,
        ]);
    }
}
