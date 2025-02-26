<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            // Admin
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Assessors
            // [
            //     'name' => 'Assessor 1',
            //     'email' => 'assessor1@example.com',
            //     'password' => Hash::make('password'),
            //     'role_id' => 2,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ],
            // [
            //     'name' => 'Assessor 2',
            //     'email' => 'assessor2@example.com',
            //     'password' => Hash::make('password'),
            //     'role_id' => 2,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ],
            // [
            //     'name' => 'Assessor 3',
            //     'email' => 'assessor3@example.com',
            //     'password' => Hash::make('password'),
            //     'role_id' => 3,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ],

            // // Users yang akan dinilai (role_id 4)
            // [
            //     'name' => 'User 1',
            //     'email' => 'user1@example.com',
            //     'password' => Hash::make('password'),
            //     'role_id' => 4,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ],
            // [
            //     'name' => 'User 2',
            //     'email' => 'user2@example.com',
            //     'password' => Hash::make('password'),
            //     'role_id' => 4,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ],
            // [
            //     'name' => 'User 3',
            //     'email' => 'user3@example.com',
            //     'password' => Hash::make('password'),
            //     'role_id' => 4,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ],
        ]);

        // Tambahkan 10 user tambahan dengan role_id 4
        $additionalUsers = [];
        for ($i = 4; $i <= 13; $i++) {
            $additionalUsers[] = [
                'name' => "User $i",
                'email' => "user$i@example.com",
                'password' => Hash::make('password'),
                'role_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }

        DB::table('users')->insert($additionalUsers);
    }
}
