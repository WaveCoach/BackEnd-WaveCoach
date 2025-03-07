<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\ScheduleDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SchedulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $coaches = User::whereIn('role_id', [2, 3])->pluck('id')->toArray();

        if (empty($coaches)) {
            return;
        }

        $students = User::where('role_id', 4)->pluck('id')->toArray();

        if (empty($students)) {
            return;
        }

        $schedules = [
            [
                'coach_id' => 2,
                'location_id' => 1,
                'date' => Carbon::parse('2024-03-10'),
                'start_time' => '10:00:00',
                'end_time' => '12:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coach_id' => 2,
                'location_id' => 2,
                'date' => Carbon::parse('2024-03-12'),
                'start_time' => '14:30:00',
                'end_time' => '16:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coach_id' => $coaches[array_rand($coaches)],
                'location_id' => 3,
                'date' => Carbon::parse('2024-03-15'),
                'start_time' => '09:00:00',
                'end_time' => '11:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($schedules as $scheduleData) {
            $createdSchedule = Schedule::create($scheduleData);

            $selectedStudents = collect($students)->shuffle()->take(rand(2, 5));

            foreach ($selectedStudents as $studentId) {
                ScheduleDetail::create([
                    'schedule_id' => $createdSchedule->id,
                    'user_id' => $studentId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
