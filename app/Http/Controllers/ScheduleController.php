<?php

namespace App\Http\Controllers;

use App\Models\schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{

    public function index()
    {
        $schedule = schedule::orderBy('created_at', 'desc')->get();
        return view('pages.schedule.index', compact('schedule'));
    }



}
