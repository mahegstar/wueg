<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Language::create([
            'language' => 'English',
            'code' => 'en',
            'status' => 1,
            'type' => 1,
            'default_active' => 1,
        ]);
    }
}