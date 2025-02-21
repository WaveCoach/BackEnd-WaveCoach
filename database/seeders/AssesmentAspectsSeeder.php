<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssesmentAspectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('assesment_aspects')->insert([
            ['assesment_categories_id' => 1, 'name' => 'Kecepatan', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 1, 'name' => 'Ketahanan', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 2, 'name' => 'Teknik', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 2, 'name' => 'Konsistensi', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 3, 'name' => 'Fleksibilitas', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 3, 'name' => 'Keseimbangan', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 4, 'name' => 'Koordinasi', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 4, 'name' => 'Stamina', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 5, 'name' => 'Daya Tahan', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 5, 'name' => 'Keakuratan', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 6, 'name' => 'Ketepatan Gerakan', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 6, 'name' => 'Daya Ledak', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 7, 'name' => 'Kekuatan', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 7, 'name' => 'Ketahanan Otot', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 8, 'name' => 'Kontrol Pernapasan', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 8, 'name' => 'Ritme Gerakan', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 9, 'name' => 'Tekanan Air', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 9, 'name' => 'Efisiensi Energi', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 10, 'name' => 'Ketahanan Mental', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assesment_categories_id' => 10, 'name' => 'Adaptasi Lingkungan', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
