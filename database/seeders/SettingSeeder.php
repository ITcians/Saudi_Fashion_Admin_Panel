<?php

namespace Database\Seeders;

use App\Models\SettingModel;

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SettingModel::create([
            'key'=>"brand_name",
            'value'=>'Saudi Fashion',
        ]);
        SettingModel::create([
            'key'=>"brand_logo",
            'value'=>'/images/logo.png',
        ]);
        SettingModel::create([
            'key'=>"brand_logo_white",
            'value'=>'/images/logo-white.png',
        ]);
        SettingModel::create([
            'key'=>"default_currency",
            'value'=>'SAR',
        ]);
        SettingModel::create([
            'key'=>"service_fee",
            'value'=>'20',
        ]);
        SettingModel::create([
            'key'=>"delivery_fee",
            'value'=>'12',
        ]);
        SettingModel::create([
            'key'=>"tax",
            'value'=>'10',
        ]);
        SettingModel::create([
            'key'=>"smtp_email",
            'value'=>'test@maventics.com',
        ]);
        SettingModel::create([
            'key'=>"smtp_password",
            'value'=>'suxbEw-bujxa0-bygpyc',
        ]);
        SettingModel::create([
            'key'=>"smtp_server",
            'value'=>'smtp.hostinger.com',
        ]);
        SettingModel::create([
            'key'=>"smtp_port",
            'value'=>'465',
        ]);
        SettingModel::create([
            'key'=>"smtp_encryption",
            'value'=>'SSL',
        ]);
        SettingModel::create([
            'key'=>"smtp_sender_name",
            'value'=>'Maventics',
        ]);
        SettingModel::create([
            'key'=>"fcm_server_key",
            'value'=>'--',
        ]);
    }
}
