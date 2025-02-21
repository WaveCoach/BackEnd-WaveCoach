<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua user_id yang memiliki role_id 4
        $users = DB::table('users')
            ->where('role_id', 4)
            ->pluck('id')
            ->toArray();

        if (count($users) < 5) {
            return; // Pastikan ada cukup user dengan role_id 4
        }

        DB::table('schedule_details')->insert([
            [
                'schedule_id' => 1,
                'user_id'     => $users[0], // User pertama dengan role_id 4
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'schedule_id' => 1,
                'user_id'     => $users[1],
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'schedule_id' => 2,
                'user_id'     => $users[2],
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'schedule_id' => 3,
                'user_id'     => $users[0],
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'schedule_id' => 3,
                'user_id'     => $users[3],
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
        ]);
    }
}
