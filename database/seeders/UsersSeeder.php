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

        foreach ($coaches as $index => $coach) {
            $roleId = in_array($index + 1, [3, 4, 5]) ? 3 : 2; // Coach 3, 4, 5 -> role_id = 3, lainnya tetap 2

            $userId = DB::table('users')->insertGetId([
                'name' => $coach['name'],
                'email' => $coach['email'],
                'password' => Hash::make('password'),
                'role_id' => $roleId,
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
            ['name' => 'Student One', 'email' => 'student1@gmail.com', 'tanggal_lahir' => '2010-01-01', 'jenis_kelamin' => 'L'],
            ['name' => 'Student Two', 'email' => 'student2@gmail.com', 'tanggal_lahir' => '2009-01-01', 'jenis_kelamin' => 'P'],
            ['name' => 'Student Three', 'email' => 'student3@gmail.com', 'tanggal_lahir' => '2008-01-01', 'jenis_kelamin' => 'L'],
            ['name' => 'Student Four', 'email' => 'student4@gmail.com', 'tanggal_lahir' => '2007-01-01', 'jenis_kelamin' => 'P'],
            ['name' => 'Student Five', 'email' => 'student5@gmail.com', 'tanggal_lahir' => '2010-01-01', 'jenis_kelamin' => 'L'],
            ['name' => 'Student Six', 'email' => 'student6@gmail.com', 'tanggal_lahir' => '2009-01-01', 'jenis_kelamin' => 'P'],
            ['name' => 'Student Seven', 'email' => 'student7@gmail.com', 'tanggal_lahir' => '2008-01-01', 'jenis_kelamin' => 'L'],
            ['name' => 'Student Eight', 'email' => 'student8@gmail.com', 'tanggal_lahir' => '2007-01-01', 'jenis_kelamin' => 'P'],
            ['name' => 'Student Nine', 'email' => 'student9@gmail.com', 'tanggal_lahir' => '2010-01-01', 'jenis_kelamin' => 'L'],
            ['name' => 'Student Ten', 'email' => 'student10@gmail.com', 'tanggal_lahir' => '2009-01-01', 'jenis_kelamin' => 'P'],
        ];

        foreach ($students as $student) {
            $tahunMasuk = Carbon::now()->format('y');
            $lastStudent = DB::table('students')
                ->where('nis', 'like', $tahunMasuk . '%')
                ->orderBy('nis', 'desc')
                ->first();

            $nextNumber = $lastStudent ? ((int)substr($lastStudent->nis, 2) + 1) : 1;

            $nis = $tahunMasuk . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

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
                'tanggal_lahir' => $student['tanggal_lahir'],
                'jenis_kelamin' => $student['jenis_kelamin'],
                'nis' => $nis,
                'type' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

    }
}
