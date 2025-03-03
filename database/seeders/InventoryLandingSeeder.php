<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryLandingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('inventory_landings')->insert([
            [
                'inventory_landings_id' => null,
                'inventory_id' => 1,
                'mastercoach_id' => 22,
                'coach_id' => 2,
                'qty_in' => 2,
                'qty_out' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'inventory_landings_id' => 1,
                'inventory_id' => 1,
                'inventory_landings_id' => 1,
                'mastercoach_id' => 22,
                'coach_id' => 2,
                'qty_in' => null,
                'qty_out' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'inventory_landings_id' => 1,
                'inventory_id' => 1,
                'inventory_landings_id' => 1,
                'mastercoach_id' => 22,
                'coach_id' => 2,
                'qty_in' => null,
                'qty_out' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'inventory_landings_id' => null,
                'inventory_id' => 2,
                'mastercoach_id' => 23,
                'coach_id' => 3,
                'qty_in' => 5,
                'qty_out' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'inventory_landings_id' => 4,
                'inventory_id' => 2,
                'mastercoach_id' => 23,
                'coach_id' => 3,
                'qty_in' => null,
                'qty_out' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
