<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('locations')->insert([
            [
                'name' => 'Head Office',
                'address' => 'Jl. Sudirman No. 1, Jakarta',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null
            ],
            [
                'name' => 'Branch Office Bandung',
                'address' => 'Jl. Asia Afrika No. 10, Bandung',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null
            ],
            [
                'name' => 'Branch Office Surabaya',
                'address' => 'Jl. Tunjungan No. 25, Surabaya',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null
            ],
            [
                'name' => 'Branch Office Medan',
                'address' => 'Jl. Gatot Subroto No. 50, Medan',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null
            ],
        ]);
    }
}
