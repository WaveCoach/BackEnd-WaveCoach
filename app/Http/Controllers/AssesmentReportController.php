<?php

namespace App\Http\Controllers;

use App\Models\assesment;
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
        $assesment = assesment::where('user_id', $id)->get();
        // dd($assesment);
        $nilai = assessments_detail::where('assessment_id', $assesment->id)->get();
    }

}
