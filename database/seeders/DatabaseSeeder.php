<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
        ]);

        User::query()->updateOrCreate(
            ['email' => 'admin@mci.test'],
            [
                'name' => 'Admin MCI',
                'role' => UserRole::Admin,
                'password' => 'password',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'manager@mci.test'],
            [
                'name' => 'Manager MCI',
                'role' => UserRole::Manager,
                'password' => 'password',
            ]
        );
    }
}
