<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssesmentReportController extends Controller
{

    public function index()
    {
        return view('pages.assesment_report.index');
    }

}
