<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'system_name', 'value' => 'Request Workflow System'],
            ['key' => 'registration_mode', 'value' => 'public'],
            ['key' => 'items_per_page', 'value' => '15'],
            ['key' => 'date_format', 'value' => 'Y-m-d'],
            ['key' => 'timezone', 'value' => 'UTC'],
            ['key' => 'enable_notifications', 'value' => 'true'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}

