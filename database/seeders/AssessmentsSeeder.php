<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssessmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ambil semua assessor (role_id 2 & 3)
        $assessors = DB::table('users')->whereIn('role_id', [2, 3])->pluck('id');

        // Ambil semua user yang akan dinilai (role_id 4)
        $users = DB::table('users')->where('role_id', 4)->pluck('id');

        // Jika ada data yang ditemukan
        if ($assessors->isNotEmpty() && $users->isNotEmpty()) {
            $data = [];
            foreach ($users as $user_id) {
                // Pilih assessor secara acak
                $assessor_id = $assessors->random();

                $data[] = [
                    'user_id' => $user_id,
                    'assessor_id' => $assessor_id,
                    'assessment_date' => Carbon::now()->subDays(rand(1, 30)),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            DB::table('assessments')->insert($data);
        }
    }
}
