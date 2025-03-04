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
                'mastercoach_id' => 3,
                'coach_id' => 6,
                'qty_in' => null,
                'qty_out' => 2,
                'status' => 'dipinjam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'inventory_landings_id' => 1,
                'inventory_id' => 1,
                'inventory_landings_id' => 1,
                'mastercoach_id' => 3,
                'coach_id' => 6,
                'qty_in' => 1,
                'qty_out' => null,
                'status' => 'dikembalikan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'inventory_landings_id' => 1,
                'inventory_id' => 1,
                'inventory_landings_id' => 1,
                'mastercoach_id' => 3,
                'coach_id' => 6,
                'qty_in' => 1,
                'qty_out' => null,
                'status' => 'dikembalikan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'inventory_landings_id' => null,
                'inventory_id' => 2,
                'mastercoach_id' => 4,
                'coach_id' => 7,
                'qty_in' => null,
                'qty_out' => 5,
                'status' => 'dipinjam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'inventory_landings_id' => 4,
                'inventory_id' => 2,
                'mastercoach_id' => 4,
                'coach_id' => 7,
                'qty_in' => 5,
                'qty_out' => null,
                'status' => 'dikembalikan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
