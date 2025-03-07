<?php

namespace Database\Seeders;

use App\Models\RescheduleRequest;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RescheduleRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        RescheduleRequest::insert([
            [
                'schedule_id' => 1,
                'coach_id' => 2,
                // 'requested_date' => Carbon::now()->addDays(3)->toDateString(),
                // 'requested_time' => '14:00:00',
                'reason' => 'Coach unavailable due to emergency',
                'status' => 'pending',
                'admin_id' => null,
                'response_message' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 2,
                'coach_id' => 3,
                // 'requested_date' => Carbon::now()->addDays(5)->toDateString(),
                // 'requested_time' => '16:00:00',
                'reason' => 'Schedule conflict with another session',
                'status' => 'approved',
                'admin_id' => 1,
                'response_message' => 'Reschedule approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 3,
                'coach_id' => 4,
                // 'requested_date' => Carbon::now()->addWeek()->toDateString(),
                // 'requested_time' => '10:30:00',
                'reason' => 'Personal reason',
                'status' => 'rejected',
                'admin_id' => 1,
                'response_message' => 'Reschedule denied due to limited availability',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
