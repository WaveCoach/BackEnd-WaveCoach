<?php

namespace Database\Seeders;

use App\Models\schedule;
use App\Models\schedule_detail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchedulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

     public function run()
     {
         // Seeder for users
         $users = User::factory()->count(10)->create();

         // Seeder for schedules
         $schedules = [
             [
                 'coach_id' => 1,
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
                 'coach_id' => 3,
                 'location_id' => 3,
                 'date' => Carbon::parse('2024-03-15'),
                 'start_time' => '09:00:00',
                 'end_time' => '11:00:00',
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
         ];

         foreach ($schedules as $schedule) {
             $createdSchedule = schedule::create($schedule);

             // Assign random users to the schedule
             $selectedUsers = $users->random(rand(2, 5));
             foreach ($selectedUsers as $user) {
                 schedule_detail::create([
                     'schedule_id' => $createdSchedule->id,
                     'user_id' => $user->id,
                     'created_at' => now(),
                     'updated_at' => now(),
                 ]);
             }
         }
     }
}
