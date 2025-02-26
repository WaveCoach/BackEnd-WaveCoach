<?php

namespace App\Http\Controllers;

use App\Models\coaches;
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
        $schedules = Schedule::with(['coach', 'students'])->get();
        return view('pages.schedule.index', compact('schedules'));
    }


    public function store(Request $request) {
        // dd($request->all());
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'coach_id' => 'nullable',
            'location_id' => 'nullable',
            'student_id' => 'required|array',
            'student_id.*' => 'required',
            'maps' => 'required',
            'email' => 'required'
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

            coaches::create([
                'user_id' => $coach->id
            ]);
        }

        if (is_numeric($request->location_id)) {
            $locationId = $request->location_id;
        } else {
            $location = Location::firstOrCreate(
                ['name' => $request->location_id],
                ['address' => $request->address],
                ['maps' => $request->maps],
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



    public function show($id)
    {
        $schedule = Schedule::with(['coach', 'students', 'location'])->findOrFail($id);
        return view('pages.schedule.show', compact('schedule'));
    }


    public function edit($id) {
        $coaches = User::whereIn('role_id', [2, 3])->get(); // Ubah dari $coach ke $coaches
        $students = User::where('role_id', 4)->get();
        $locations = Location::all();
        $schedule = Schedule::with(['coach', 'students', 'location'])->findOrFail($id);

        return view('pages.schedule.edit', compact('coaches', 'students', 'locations', 'schedule'));
    }

    public function getStudent(Request $request) {
        $coach = schedule::where('coach_id', $request->coach_id)->latest()->first();

        if (!$coach) {
            return response()->json(['error' => 'Coach tidak ditemukan'], 404);
        }

        $students = schedule_detail::with('user')->where('schedule_id', $coach->id)->get();

        return response()->json(['students' => $students]);
    }


    public function update(Request $request, $id) {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'coach_id' => 'nullable',
            'location_id' => 'nullable',
            'student_id' => 'required|array',
            'student_id.*' => 'required',
            'maps' => 'required',
            'email' => 'required'
        ]);

        $schedule = Schedule::findOrFail($id);

        if (!is_numeric($request->coach_id)) {
            $coach = User::firstOrCreate(
                ['name' => $request->coach_id],
                [
                    'role_id' => 2,
                    'email' => $request->email,
                    'password' => Hash::make('12345678')
                ]
            );
            $coachId = $coach->id;

            coaches::create([
                'user_id' => $coach->id
            ]);

        } else {
            $coachId = $request->coach_id;
        }

        if (!is_numeric($request->location_id)) {
            $location = Location::firstOrCreate(
                ['name' => $request->location_id],
                ['address' => $request->address],
                ['maps' => $request->maps]
            );
            $locationId = $location->id;
        } else {
            $locationId = $request->location_id;
        }

        $schedule->update([
            'date' => $request->date ?? $schedule->date,
            'start_time' => $request->start_time ?? $schedule->start_time,
            'end_time' => $request->end_time ?? $schedule->end_time,
            'coach_id' => $coachId ?? $schedule->coach_id,
            'location_id' => $locationId ?? $schedule->location_id,
        ]);

        $existingStudentIds = $schedule->students->pluck('id')->toArray();
        $newStudentIds = $request->student_id;

        schedule_detail::where('schedule_id', $schedule->id)
            ->whereNotIn('user_id', $newStudentIds)
            ->delete();

        foreach ($newStudentIds as $studentId) {
            if (!in_array($studentId, $existingStudentIds)) {
                schedule_detail::create([
                    'user_id' => $studentId,
                    'schedule_id' => $schedule->id,
                ]);
            }
        }

        return redirect()->route('schedule.index')->with('success', 'Schedule berhasil diperbarui.');
    }


    public function destroy($id) {
        $schedule = Schedule::find($id);
        if (!$schedule) {
            return redirect()->route('schedule.index')->with('error', 'Jadwal tidak ditemukan.');
        }
        $schedule->scheduleDetail()->delete();
        $schedule->delete();

        return redirect()->route('schedule.index')->with('success', 'Peserta berhasil dihapus.');
    }






}
