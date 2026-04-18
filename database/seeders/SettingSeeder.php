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
        Setting::setValue('company_name', 'CV MIRSA CIPTA INDONESIA');
        Setting::setValue('company_slogan', 'Designing and manufacture for Jig, SPM and Mechanical component');
        Setting::setValue('company_address', 'Jl. Raya Industri No. 123, Cikarang, Bekasi');
        Setting::setValue('company_email', 'info@mci.co.id');
        Setting::setValue('company_phone', '(021) 12345678');
    }
}
