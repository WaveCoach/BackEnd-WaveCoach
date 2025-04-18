<?php

namespace App\Http\Controllers;

use App\Models\CoachAttendance;
use App\Models\StudentAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function studentAttendance(){
        $students = User::leftJoin('student_attendances', 'users.id', '=', 'student_attendances.student_id')
        ->where('users.role_id', 4)
        ->select([
            'users.id',
            'users.name',
            DB::raw("SUM(CASE WHEN student_attendances.attendance_status = 'Hadir' THEN 1 ELSE 0 END) as total_hadir"),
            DB::raw("SUM(CASE WHEN student_attendances.attendance_status = 'Tidak Hadir' THEN 1 ELSE 0 END) as total_tidak_hadir")
        ])
        ->groupBy('users.id', 'users.name')
        ->orderBy('users.name', 'asc')
        ->get();
        // dd($students);
        return view('pages.studentAttendance.index', compact('students'));
    }

    public function studentAttendanceShow(Request $request, $id)
    {
        $query = StudentAttendance::with(['student', 'schedule'])
                    ->where('student_id', $id);

        if ($request->filled('date_start') && $request->filled('date_end')) {
            $start = Carbon::parse($request->date_start)->startOfDay();
            $end = Carbon::parse($request->date_end)->endOfDay();

            $query->whereHas('schedule', function($q) use ($start, $end) {
                $q->whereBetween('date', [$start, $end]);
            });
        }

        $schedule = $query->get();

        return view('pages.studentAttendance.show', compact('schedule'));
    }


    public function coachAttendance(){
        $coach = User::leftJoin('coach_attendances', 'users.id', '=', 'coach_attendances.coach_id')
        ->whereIn('users.role_id', [2, 3])
        ->select([
            'users.id',
            'users.name',
            DB::raw("SUM(CASE WHEN coach_attendances.attendance_status = 'Hadir' THEN 1 ELSE 0 END) as total_hadir"),
            DB::raw("SUM(CASE WHEN coach_attendances.attendance_status = 'Tidak Hadir' THEN 1 ELSE 0 END) as total_tidak_hadir")
        ])
        ->groupBy('users.id', 'users.name')
        ->orderBy('users.name', 'asc')
        ->get();
        return view('pages.coachAttendance.index', compact('coach'));
    }

    public function coachAttendanceShow(Request $request, $id)
    {
        $query = CoachAttendance::with(['coach', 'schedule'])
        ->where('coach_id', $id);

        if ($request->filled('date_start') && $request->filled('date_end')) {
        $start = Carbon::parse($request->date_start)->startOfDay();
        $end = Carbon::parse($request->date_end)->endOfDay();
        $query->whereBetween('created_at', [$start, $end]);
        }

        $schedule = $query->get();

        return view('pages.coachAttendance.show', compact('schedule'));
    }
}
