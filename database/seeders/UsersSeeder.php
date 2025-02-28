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
        ]);

        $coaches = [
            ['name' => 'Coach One', 'email' => 'coach1@gmail.com'],
            ['name' => 'Coach Two', 'email' => 'coach2@gmail.com'],
            ['name' => 'Coach Three', 'email' => 'coach3@gmail.com'],
            ['name' => 'Coach Four', 'email' => 'coach4@gmail.com'],
            ['name' => 'Coach Five', 'email' => 'coach5@gmail.com'],
            ['name' => 'Coach Six', 'email' => 'coach6@gmail.com'],
            ['name' => 'Coach Seven', 'email' => 'coach7@gmail.com'],
            ['name' => 'Coach Eight', 'email' => 'coach8@gmail.com'],
            ['name' => 'Coach Nine', 'email' => 'coach9@gmail.com'],
            ['name' => 'Coach Ten', 'email' => 'coach10@gmail.com'],
        ];

        foreach ($coaches as $coach) {
            $userId = DB::table('users')->insertGetId([
                'name' => $coach['name'],
                'email' => $coach['email'],
                'password' => Hash::make('password'),
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            DB::table('coaches')->insert([
                'user_id' => $userId,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }


        $students = [
            ['name' => 'Student One', 'email' => 'student1@gmail.com', 'usia' => '15', 'jenis_kelamin' => 'L'],
            ['name' => 'Student Two', 'email' => 'student2@gmail.com', 'usia' => '16', 'jenis_kelamin' => 'P'],
            ['name' => 'Student Three', 'email' => 'student3@gmail.com', 'usia' => '17', 'jenis_kelamin' => 'L'],
            ['name' => 'Student Four', 'email' => 'student4@gmail.com', 'usia' => '18', 'jenis_kelamin' => 'P'],
            ['name' => 'Student Five', 'email' => 'student5@gmail.com', 'usia' => '15', 'jenis_kelamin' => 'L'],
            ['name' => 'Student Six', 'email' => 'student6@gmail.com', 'usia' => '16', 'jenis_kelamin' => 'P'],
            ['name' => 'Student Seven', 'email' => 'student7@gmail.com', 'usia' => '17', 'jenis_kelamin' => 'L'],
            ['name' => 'Student Eight', 'email' => 'student8@gmail.com', 'usia' => '18', 'jenis_kelamin' => 'P'],
            ['name' => 'Student Nine', 'email' => 'student9@gmail.com', 'usia' => '15', 'jenis_kelamin' => 'L'],
            ['name' => 'Student Ten', 'email' => 'student10@gmail.com', 'usia' => '16', 'jenis_kelamin' => 'P'],
        ];

        foreach ($students as $student) {
            $userId = DB::table('users')->insertGetId([
                'name' => $student['name'],
                'email' => $student['email'],
                'password' => Hash::make('password'),
                'role_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            DB::table('students')->insert([
                'user_id' => $userId,
                'usia' => $student['usia'],
                'jenis_kelamin' => $student['jenis_kelamin'],
                'type' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

    }
}
