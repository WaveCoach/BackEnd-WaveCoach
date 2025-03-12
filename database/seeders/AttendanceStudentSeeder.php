<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('student_attendances')->insert([
            [
                'schedule_id' => 1,
                'student_id' => 1,
                'attendance_status' => 1, // Hadir
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 1,
                'student_id' => 2,
                'attendance_status' => 0, // Tidak Hadir
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 2,
                'student_id' => 3,
                'attendance_status' => 1, // Hadir
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 2,
                'student_id' => 4,
                'attendance_status' => 0, // Tidak Hadir
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 3,
                'student_id' => 5,
                'attendance_status' => 1, // Hadir
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
