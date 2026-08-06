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
        Setting::firstOrCreate(
            ['id' => 1],
            [
                'store_name' => 'Lucky Mart',
                'store_phone' => '012345678',
                'store_email' => 'info@luckymart.com',
                'store_address' => 'Phnom Penh',
                'store_logo' => null,
            ]
        );
    }
}
