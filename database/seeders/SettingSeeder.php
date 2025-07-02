<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['type' => 'app_name', 'message' => 'Elite Quiz'],
            ['type' => 'system_timezone', 'message' => 'Asia/Kolkata'],
            ['type' => 'system_timezone_gmt', 'message' => '+05:30'],
            ['type' => 'app_version', 'message' => '1.0.0'],
            ['type' => 'force_update', 'message' => '0'],
            ['type' => 'language_mode', 'message' => '1'],
            ['type' => 'option_e_mode', 'message' => '1'],
            ['type' => 'daily_quiz_mode', 'message' => '1'],
            ['type' => 'contest_mode', 'message' => '1'],
            ['type' => 'battle_mode_one', 'message' => '1'],
            ['type' => 'battle_mode_group', 'message' => '1'],
            ['type' => 'audio_mode_question', 'message' => '1'],
            ['type' => 'in_app_ads_mode', 'message' => '0'],
            ['type' => 'in_app_purchase_mode', 'message' => '0'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}