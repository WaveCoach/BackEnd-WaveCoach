<?php

namespace App\Http\Controllers;

use App\Models\assesment;
use App\Models\Assessment;
use App\Models\AssessmentDetail;
use App\Models\assessments_detail;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class AssesmentReportController extends Controller
{

    public function index()
    {
        $users = User::where('role_id', 4)->whereHas('assessments')->get();

        return view('pages.assesment_report.index', compact('users'));
    }

    public function show($id){
        $assesment = Assessment::with(['student', 'category', 'coach'])->where('student_id', $id)->get();
        $user = User::find($id);

        return view('pages.assesment_report.show', compact('assesment', 'user'));
    }

    public function showPdf($id){
        $nilai = AssessmentDetail::with(['assessment', 'aspect'])->where('assessment_id', $id)->get();
        $assessment = Assessment::with(['student', 'category', 'coach', 'schedule.location'])->where('id', $id)->first();
        $student = Student::where('user_id', $assessment->student_id)->first();
        return view('pages.assesment_report.raport', compact('nilai', 'assessment', 'student'));
    }


}
