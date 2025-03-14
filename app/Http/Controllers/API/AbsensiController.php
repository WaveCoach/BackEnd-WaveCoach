<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\CoachAttendance;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends BaseController
{
    public function coachAbsent(Request $request)
    {
        $validated = $request->validate([
            'attendance_status' => 'required|string',
            'remarks' => 'nullable|string',
            'proof' => 'nullable|image|max:2048',
            'schedule_id' => 'required'
        ]);

        // Cek apakah absensi sudah ada
        $existingAttendance = CoachAttendance::where('coach_id', Auth::user()->id)
            ->where('schedule_id', $request->schedule_id)
            ->exists();

        if ($existingAttendance) {
            return $this->ErrorResponse('Anda sudah melakukan absensi untuk jadwal ini!', 400);
        }

        if ($request->hasFile('proof')) {
            $path = $request->file('proof')->store('public/proofs');
            $validated['proof'] = Storage::url($path);
        }

        $attendance = CoachAttendance::create([
            'coach_id' => Auth::user()->id,
            'attendance_status' => $validated['attendance_status'],
            'remarks' => $validated['remarks'] ?? null,
            'proof' => $validated['proof'] ?? null,
            'schedule_id' => $request->schedule_id
        ]);

        return $this->SuccessResponse($attendance, 'Absensi berhasil disimpan', 201);
    }

    public function studentAbsent(Request $request)
    {
        $validated = $request->validate([
            'attendance_status' => 'required|string|in:Hadir,Tidak Hadir',
            'student_id' => 'required|exists:users,id',
            'schedule_id' => 'required|exists:schedules,id',
        ]);

        // Cek apakah absensi sudah ada
        $existingAttendance = StudentAttendance::where('student_id', $request->student_id)
            ->where('schedule_id', $request->schedule_id)
            ->exists();

        if ($existingAttendance) {
            return $this->ErrorResponse('Siswa ini sudah melakukan absensi untuk jadwal ini!', 400);
        }

        $attendance = StudentAttendance::create([
            'student_id' => $request->student_id,
            'attendance_status' => $validated['attendance_status'],
            'schedule_id' => $request->schedule_id
        ]);

        return $this->SuccessResponse($attendance, 'Absensi berhasil disimpan', 201);
    }

}
