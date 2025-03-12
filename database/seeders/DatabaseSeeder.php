<?php

namespace Database\Seeders;

use App\Models\RescheduleRequest;
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
            RolesTableSeeder::class,
            UsersSeeder::class,            // Seeder untuk pengguna (admin, assessor, user)
            LocationsSeeder::class,        // Seeder untuk tabel locations
            SchedulesSeeder::class,        // Seeder untuk tabel schedules
            RescheduleRequestSeeder::class,
            NotificationSeeder::class,
            InventoryManagementSeeder::class,
            InventorySeeder::class,
            AssessmentAspectSeeder::class,
            AssessmentCategoriesSeeder::class
        ]);

    }
}
