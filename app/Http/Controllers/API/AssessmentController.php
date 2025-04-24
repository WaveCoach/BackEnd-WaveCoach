<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAspect;
use App\Models\AssessmentCategory;
use App\Models\AssessmentDetail;
use App\Models\Schedule;
use App\Models\ScheduleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssessmentController extends BaseController
{

    public function getStudent($id){
        $students = ScheduleDetail::with('student')
        ->where('schedule_id', $id)
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->student->id,
                'name' => $item->student->name,
            ];
        });

        return $this->SuccessResponse($students, 'Data siswa berhasil diambil');
    }

    public function getCategory($package_id)
    {
        $category = AssessmentCategory::select('id', 'name')
            ->whereHas('packages', function ($query) use ($package_id) {
                $query->where('package_id', $package_id);
            })
            ->get();

        return $this->SuccessResponse($category, 'Data kategori berhasil diambil');
    }


    public function getAspect($CategoryId){
        $aspect = AssessmentAspect::select('id', 'assessment_categories_id', 'name', 'desc')->where('assessment_categories_id', $CategoryId)->get();
        if ($aspect->isEmpty()) {
            return $this->ErrorResponse('Data aspek tidak ditemukan', 404);
        }
        return $this->SuccessResponse($aspect, 'Data aspek berhasil diambil');
    }

    public function postAssessment(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'assessment_date' => 'required|date',
            'package_id' => 'required|integer',
            'schedule_id' => 'required|integer',
            'categories' => 'required|array',
            'categories.*.assessment_category_id' => 'required|integer|exists:assessment_categories,id',
            'categories.*.details' => 'required|array',
            'categories.*.details.*.aspect_id' => 'required|exists:assessment_aspects,id',
            'categories.*.details.*.score' => 'required|numeric|min:0|max:100',
            'categories.*.details.*.remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['categories'] as $category) {
                // Cek duplikasi per kategori
                $exists = Assessment::where([
                    'student_id' => $validated['student_id'],
                    'assessor_id' => Auth::id(),
                    'schedule_id' => $validated['schedule_id'],
                    'package_id' => $validated['package_id'],
                    'assessment_date' => $validated['assessment_date'],
                    'assessment_category_id' => $category['assessment_category_id'],
                ])->exists();

                if ($exists) {
                    DB::rollBack();
                    return $this->ErrorResponse("Data assessment untuk kategori ID {$category['assessment_category_id']} sudah ada.", 400);
                }

                // Simpan assessment utama
                $assessment = Assessment::create([
                    'student_id' => $validated['student_id'],
                    'assessor_id' => Auth::id(),
                    'schedule_id' => $validated['schedule_id'],
                    'package_id' => $validated['package_id'],
                    'assessment_date' => $validated['assessment_date'],
                    'assessment_category_id' => $category['assessment_category_id'],
                ]);

                // Simpan detail
                $details = array_map(fn($detail) => [
                    'assessment_id' => $assessment->id,
                    'aspect_id' => $detail['aspect_id'],
                    'score' => $detail['score'],
                    'remarks' => $detail['remarks'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $category['details']);

                AssessmentDetail::insert($details);
            }

            DB::commit();
            return $this->SuccessResponse('Semua assessment berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->ErrorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }


    public function getHistory(Request $request)
    {
        $query = Assessment::with(['student', 'assessor', 'package', 'category', 'details'])
            ->where('assessor_id', Auth::user()->id)
            ->orderBy('created_at', 'desc');

        $search = $request->input('search');

        if ($search) {
            $query->whereHas('package', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('category', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $assessments = $query->get()->map(function ($assessment) {
            // Hitung nilai rata-rata dari detail skor
            $scores = $assessment->details->pluck('score')->filter();
            $averageScore = $scores->count() > 0 ? round($scores->avg(), 1) : null;

            // Ambil KKM dari kategori
            $kkm = $assessment->category->kkm ?? null;

            // Tentukan status lulus
            $status = null;
            if (!is_null($averageScore) && !is_null($kkm)) {
                $status = $averageScore >= $kkm ? 'Lulus' : 'Tidak Lulus';
            }

            return [
                'id' => $assessment->id,
                'date' => $assessment->assessment_date,
                'created_at' => $assessment->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
                'student' => [
                    'id' => $assessment->student->id ?? null,
                    'name' => $assessment->student->name ?? null,
                ],
                'assessor' => [
                    'id' => $assessment->assessor->id ?? null,
                    'name' => $assessment->assessor->name ?? null,
                ],
                'package' => [
                    'id' => $assessment->package->id ?? null,
                    'name' => $assessment->package->name ?? null,
                ],
                'category' => [
                    'id' => $assessment->category->id ?? null,
                    'name' => $assessment->category->name ?? null,
                ],
                'average_score' => $averageScore,
                'status' => $status,
            ];
        });

        return $this->SuccessResponse($assessments, 'Data kategori berhasil diambil');
    }


    public function getDetailHistory($id)
    {
        $assessment = Assessment::with(['student', 'assessor', 'package', 'category', 'schedule'])->find($id);

        if (!$assessment) {
            return $this->ErrorResponse('Data tidak ditemukan', 404);
        }

        $assessmentDetails = AssessmentDetail::where('assessment_id', $id)->get()->map(function ($detail) {
            return [
            'aspect_id' => $detail->aspect_id,
            'aspect_name' => $detail->aspect->name,
            'aspect_desc' => $detail->aspect->desc,
            'score' => $detail->score,
            'remarks' => $detail->remarks,
            ];
        });

        return $this->SuccessResponse([
            'id' => $assessment->id,
            'date' => $assessment->assessment_date,
            'schedule_id' => $assessment->schedule_id ?? null,
            'schedule_date' => $assessment->schedule->date ?? null,
            'schedule_start_time' => $assessment->schedule->start_time ?? null,
            'schedule_end_time' => $assessment->schedule->end_time ?? null,
            'student_id' => $assessment->student->id ?? null,
            'student_name' => $assessment->student->name ?? null,
            'assessor_id' => $assessment->assessor->id ?? null,
            'assessor_name' => $assessment->assessor->name ?? null,
            'package_id' => $assessment->package->id ?? null,
            'package_name' => $assessment->package->name ?? null,
            'category_id' => $assessment->category->id ?? null,
            'category_name' => $assessment->category->name ?? null,
            'details' => $assessmentDetails,
        ], 'Data detail history berhasil diambil');
    }
}
