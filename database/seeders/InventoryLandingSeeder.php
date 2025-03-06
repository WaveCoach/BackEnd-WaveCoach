<?php

namespace Database\Seeders;

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
                'tanggal_pinjam' => now(),
                'tanggal_kembali' => now()->addDays(7), // Batas waktu pengembalian
                'tanggal_dikembalikan' => null, // Belum dikembalikan
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'inventory_landings_id' => 1,
                'inventory_id' => 1,
                'mastercoach_id' => 3,
                'coach_id' => 6,
                'qty_in' => 1,
                'qty_out' => null,
                'status' => 'dikembalikan',
                'tanggal_pinjam' => now()->subDays(10),
                'tanggal_kembali' => now()->subDays(3), // Seharusnya dikembalikan 3 hari lalu
                'tanggal_dikembalikan' => now()->subDays(2), // Sebenarnya dikembalikan kemarin
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
                'tanggal_pinjam' => now(),
                'tanggal_kembali' => now()->addDays(10), // Batas pengembalian
                'tanggal_dikembalikan' => null, // Belum dikembalikan
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
                'tanggal_pinjam' => now()->subDays(15),
                'tanggal_kembali' => now()->subDays(5), // Seharusnya dikembalikan 5 hari lalu
                'tanggal_dikembalikan' => now()->subDays(4), // Sebenarnya dikembalikan 4 hari lalu
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
