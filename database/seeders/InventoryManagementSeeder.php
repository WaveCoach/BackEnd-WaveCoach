<?php

namespace Database\Seeders;

use App\Models\InventoryManagement;
use Illuminate\Database\Seeder;

class InventoryManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['mastercoach_id' => 3, 'inventory_id' => 1, 'qty' => 10],
            ['mastercoach_id' => 3, 'inventory_id' => 2, 'qty' => 15],
            ['mastercoach_id' => 5, 'inventory_id' => 3, 'qty' => 12],
            ['mastercoach_id' => 5, 'inventory_id' => 4, 'qty' => 5],
        ];

        foreach ($data as $item) {
            InventoryManagement::create($item);
        }
    }
}
