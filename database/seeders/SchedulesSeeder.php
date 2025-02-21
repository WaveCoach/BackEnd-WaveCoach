<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchedulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('schedules')->insert([
            [
                'coach_id'    => 1,
                'location_id' => 1,
                'date'        => '2024-03-01',
                'start_time'  => '08:00:00',
                'end_time'    => '10:00:00',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'coach_id'    => 2,
                'location_id' => 2,
                'date'        => '2024-03-02',
                'start_time'  => '09:00:00',
                'end_time'    => '11:00:00',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'coach_id'    => 3,
                'location_id' => 3,
                'date'        => '2024-03-03',
                'start_time'  => '07:30:00',
                'end_time'    => '09:30:00',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'coach_id'    => 1,
                'location_id' => 2,
                'date'        => '2024-03-04',
                'start_time'  => '10:00:00',
                'end_time'    => '12:00:00',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'coach_id'    => 2,
                'location_id' => 3,
                'date'        => '2024-03-05',
                'start_time'  => '13:00:00',
                'end_time'    => '15:00:00',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
        ]);
    }
}
