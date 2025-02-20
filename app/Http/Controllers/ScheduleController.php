<?php

namespace App\Http\Controllers;

use App\Models\location;
use App\Models\schedule;
use App\Models\schedule_detail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ScheduleController extends Controller
{

    public function index()
    {
        $schedule = schedule::orderBy('created_at', 'desc')->get();
        return view('pages.schedule.index', compact('schedule'));
    }

    public function store(Request $request) {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'coach_id' => 'nullable',
            'location_id' => 'nullable',
            'student_id' => 'required|array',
            'student_id.*' => 'required',
        ]);

        if (is_numeric($request->coach_id)) {
            $coachId = $request->coach_id;
        } else {
            $coach = User::firstOrCreate(
                ['name' => $request->coach_id],
                [
                    'role_id' => 2,
                    'email' => $request->email,
                    'password' => Hash::make('12345678')
                ]
            );
            $coachId = $coach->id;
        }

        if (is_numeric($request->location_id)) {
            $locationId = $request->location_id;
        } else {
            $location = Location::firstOrCreate(
                ['name' => $request->location_id],
                ['address' => $request->address]
            );
            $locationId = $location->id;
        }

        $schedule = Schedule::create([
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'coach_id' => $coachId,
            'location_id' => $locationId,
        ]);

        foreach ($request->student_id as $studentId) {
            schedule_detail::create([
                'user_id' => $studentId,
                'schedule_id' => $schedule->id,
            ]);
        }

        return redirect()->route('schedule.index')->with('success', 'Schedule berhasil ditambahkan.');
    }


    public function create(){
        $coach = User::whereIn('role_id', [2, 3])->get();
        $students = User::where('role_id', 4)->get();
        $location = location::all();
        return view('pages.schedule.create', compact('coach', 'students', 'location'));
    }



    public function show(){
        return view('pages.schedule.show');
    }





}
