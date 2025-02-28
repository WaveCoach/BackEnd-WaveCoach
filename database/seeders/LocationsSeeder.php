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
                'maps' => 'https://goo.gl/maps/example1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null
            ],
            [
                'name' => 'Branch Office Bandung',
                'address' => 'Jl. Asia Afrika No. 10, Bandung',
                'maps' => 'https://goo.gl/maps/example2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null
            ],
            [
                'name' => 'Branch Office Surabaya',
                'address' => 'Jl. Tunjungan No. 25, Surabaya',
                'maps' => 'https://goo.gl/maps/example3',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null
            ],
            [
                'name' => 'Branch Office Medan',
                'address' => 'Jl. Gatot Subroto No. 50, Medan',
                'maps' => 'https://goo.gl/maps/example4',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null
            ],
        ]);
    }
}
