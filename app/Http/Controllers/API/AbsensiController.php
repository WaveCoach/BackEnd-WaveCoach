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
            'proof' => 'nullable|max:2048',
            'schedule_id' => 'required'
        ]);

        // Cek apakah absensi sudah ada
        $existingAttendance = CoachAttendance::where('coach_id', Auth::user()->id)
            ->where('schedule_id', $request->schedule_id)
            ->exists();

        if ($existingAttendance) {
            return $this->ErrorResponse('Anda sudah melakukan absensi untuk jadwal ini!', 400);
        }

        if ($request->has('proof')) {
            $proofData = $request->input('proof');
            $proofBase64 = base64_decode($proofData);
            $fileName = 'proofs/' . uniqid() . '.png';
            Storage::put($fileName, $proofBase64);
            $validated['proof'] = Storage::url($fileName);
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
            'student_attendance' => 'required|array',
            'student_attendance.*.student_id' => 'required|exists:users,id',
            'student_attendance.*.attendance_status' => 'required|string|in:Hadir,Tidak Hadir',
            'schedule_id' => 'required|exists:schedules,id',
        ]);

        $successfulAbsences = [];
        $failedAbsences = [];

        foreach ($validated['student_attendance'] as $attendance) {
            $studentId = $attendance['student_id'];
            $attendanceStatus = $attendance['attendance_status'];

            $existingAttendance = StudentAttendance::where('student_id', $studentId)
                ->where('schedule_id', $validated['schedule_id'])
                ->exists();

            if ($existingAttendance) {
                $failedAbsences[] = $studentId;
                continue;
            }

            $newAttendance = StudentAttendance::create([
                'student_id' => $studentId,
                'attendance_status' => $attendanceStatus,
                'schedule_id' => $validated['schedule_id']
            ]);

            $successfulAbsences[] = $newAttendance;
        }

        return $this->SuccessResponse([
            'success' => $successfulAbsences,
            'failed' => $failedAbsences,
        ], 'Absensi telah diproses', 201);
    }


}
