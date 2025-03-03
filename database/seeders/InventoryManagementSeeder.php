<?php

namespace Database\Seeders;

use App\Models\inventory_management;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventoryManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['mastercoach_id' => 3, 'inventory_id' => 1, 'qty' => 2],
            ['mastercoach_id' => 4, 'inventory_id' => 2, 'qty' => 5],
            ['mastercoach_id' => 5, 'inventory_id' => 3, 'qty' => 4],
        ];

        foreach ($data as $item) {
            inventory_management::create($item);
        }
    }
}
