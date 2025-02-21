<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssessmentsDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('assessments_details')->insert([
            ['assessment_id' => 1, 'aspect_id' => 1, 'score' => 85, 'remarks' => 'Baik', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 1, 'aspect_id' => 2, 'score' => 90, 'remarks' => 'Sangat Baik', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 2, 'aspect_id' => 3, 'score' => 80, 'remarks' => 'Cukup', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 2, 'aspect_id' => 4, 'score' => 75, 'remarks' => 'Cukup', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 3, 'aspect_id' => 5, 'score' => 95, 'remarks' => 'Sangat Baik', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 3, 'aspect_id' => 6, 'score' => 88, 'remarks' => 'Baik', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 4, 'aspect_id' => 7, 'score' => 92, 'remarks' => 'Sangat Baik', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 4, 'aspect_id' => 8, 'score' => 79, 'remarks' => 'Cukup', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 5, 'aspect_id' => 9, 'score' => 85, 'remarks' => 'Baik', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 5, 'aspect_id' => 10, 'score' => 93, 'remarks' => 'Sangat Baik', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 6, 'aspect_id' => 11, 'score' => 78, 'remarks' => 'Cukup', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 6, 'aspect_id' => 12, 'score' => 89, 'remarks' => 'Baik', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 7, 'aspect_id' => 13, 'score' => 91, 'remarks' => 'Sangat Baik', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 7, 'aspect_id' => 14, 'score' => 83, 'remarks' => 'Baik', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 8, 'aspect_id' => 15, 'score' => 76, 'remarks' => 'Cukup', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 8, 'aspect_id' => 16, 'score' => 88, 'remarks' => 'Baik', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 9, 'aspect_id' => 17, 'score' => 94, 'remarks' => 'Sangat Baik', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 9, 'aspect_id' => 18, 'score' => 77, 'remarks' => 'Cukup', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 10, 'aspect_id' => 19, 'score' => 82, 'remarks' => 'Baik', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['assessment_id' => 10, 'aspect_id' => 20, 'score' => 95, 'remarks' => 'Sangat Baik', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
