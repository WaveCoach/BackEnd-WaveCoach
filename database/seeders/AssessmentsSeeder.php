<?php

namespace Database\Seeders;

use App\Models\assesment;
use App\Models\assesment_aspect;
use App\Models\assesment_category;
use App\Models\assessments_detail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssessmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $users = User::where('role_id', 4)->get();
        $assessors = User::whereIn('role_id', [2, 3])->get();
        $categories = assesment_category::with('aspects')->get();

        foreach ($users as $user) {
            $assessor = $assessors->random();

            $assessment = Assesment::create([
                'user_id' => $user->id,
                'assessor_id' => $assessor->id,
                'assesment_date' => Carbon::now()
            ]);

            foreach ($categories as $category) {
                foreach ($category->aspects as $aspect) {
                    assessments_detail::create([
                        'assessment_id' => $assessment->id,
                        'aspect_id' => $aspect->id,
                        'score' => rand(50, 100),
                        'remarks' => 'Sample remark'
                    ]);
                }
            }
        }
    }
}
