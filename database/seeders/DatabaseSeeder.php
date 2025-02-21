<?php

namespace Database\Seeders;

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
            AssessmentsSeeder::class,      // Seeder untuk tabel assessments
            AssessmentsDetailsSeeder::class, // Seeder untuk tabel assessments_details
            LocationsSeeder::class,        // Seeder untuk tabel locations
            SchedulesSeeder::class,        // Seeder untuk tabel schedules
            ScheduleDetailsSeeder::class,  // Seeder untuk tabel schedule_details
            AssesmentAspectsSeeder::class,
            AssesmentCategoriesSeeder::class,
            InventorySeeder::class
        ]);

    }
}
