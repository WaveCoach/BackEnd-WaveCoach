<?php

namespace App\Http\Controllers;

use App\Models\assesment;
use App\Models\Assessment;
use App\Models\AssessmentDetail;
use App\Models\assessments_detail;
use App\Models\User;
use Illuminate\Http\Request;

class AssesmentReportController extends Controller
{

    public function index()
    {
        $users = User::where('role_id', 4)->get();
        return view('pages.assesment_report.index', compact('users'));
    }

    public function show($id){
        $assesment = Assessment::with('student')->where('student_id', $id)->first();
        // dd($users);

        if (!$assesment) {
            $nilai = collect(); // Empty collection if no assessment found
        } else {
            $nilai = AssessmentDetail::where('assessment_id', $assesment->id)->get();
        }

        return view('pages.assesment_report.show', compact('assesment', 'nilai'));
    }


}
