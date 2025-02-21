<?php

namespace Database\Seeders;

use App\Models\role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        role::create([
            'name' => 'Admin',
        ]);

        Role::create([
            'name' => 'Coach',
        ]);

        Role::create([
            'name' => 'Master Coach',
        ]);

        Role::create([
            'name' => 'Student',
        ]);

    }
}
