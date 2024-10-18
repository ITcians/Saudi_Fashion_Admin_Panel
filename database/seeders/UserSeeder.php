<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'fullname' => "Owais",
            'email' => "owaismustafa1000@yahoo.com",
            'phone' => "3133128915",
            'country_code' => "+92",
            'image' => "upload_images/profile_1722525580.jpg",
            'username' => "Owais2000",
            'password' => Hash::make("123456"),
            'account_status' => 200
        ]);

    }
}
