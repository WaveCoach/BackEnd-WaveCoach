<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $packages = [
            'Private Pool',
            'Private Baby',
            'Toddler',
            'Junior',
            'Senior',
            'Family Plan'
        ];

        foreach ($packages as $package) {
            Package::firstOrCreate(['name' => $package]);
        }
    }

}
