<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\CoachAttendance;
use App\Models\Notification;
use App\Models\Schedule;
use App\Models\StudentAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Pusher\Pusher;

class AbsensiController extends BaseController
{
    public function coachAbsent(Request $request)
    {
        $validated = $request->validate([
            'attendance_status' => 'required|string',
            'remarks'           => 'nullable|string',
            'proof'             => 'nullable|string',
            'schedule_id'       => 'required'
        ]);

        // Cek apakah absensi sudah ada
        $existingAttendance = CoachAttendance::where('coach_id', Auth::user()->id)
            ->where('schedule_id', $request->schedule_id)
            ->exists();

        if ($existingAttendance) {
            return $this->ErrorResponse('Anda sudah melakukan absensi untuk jadwal ini!', 400);
        }

        try {
            // Proses gambar jika ada
            $proofPath = null;
            if ($request->has('proof') && !empty($request->proof)) {
                $proofPath = $this->uploadBase64Image($request->proof, 'public/attendance_proof');
            }

            $attendance = CoachAttendance::create([
                'coach_id'          => Auth::user()->id,
                'attendance_status' => $validated['attendance_status'],
                'remarks'           => $validated['remarks'] ?? null,
                'proof'             => $proofPath,
                'schedule_id'       => $validated['schedule_id']
            ]);

            if (Str::lower($validated['attendance_status']) === 'tidak hadir') {
                $schedule = Schedule::find($request->schedule_id);
                $adminUsers = User::where('role_id', 1)->get();

                foreach ($adminUsers as $admin) {
                    Notification::create([
                        'pengirim_id' => Auth::user()->id,
                        'notifiable_type' => 'App\Models\CoachAttendance',
                        'notifiable_id' => $attendance->id,
                        'user_id' => $admin->id,
                        'title' => 'Coach Tidak Hadir',
                        'message' => 'Coach ' . Auth::user()->name . ' tidak hadir untuk jadwal tanggal ' . optional($schedule)->date . '. Silakan lakukan penjadwalan ulang.',
                        'type' => 'absen',
                        'status' => 0
                    ]);

                    $pusher = new Pusher(
                        env('PUSHER_APP_KEY'),
                        env('PUSHER_APP_SECRET'),
                        env('PUSHER_APP_ID'),
                        [
                            'cluster' => env('PUSHER_APP_CLUSTER'),
                            'useTLS' => true,
                        ]
                    );

                    $data = [
                        'message' => 'Coach ' . Auth::user()->name . ' tidak hadir untuk jadwal tanggal ' . optional($schedule)->date . '. Silakan lakukan penjadwalan ulang.',
                        'user_id' => $admin->id,
                    ];

                    $pusher->trigger('admin-channel', 'coach-absent', $data);
                }
            }

            return $this->SuccessResponse($attendance, 'Absensi berhasil disimpan', 201);

        } catch (\Exception $e) {

            return $this->ErrorResponse('Gagal menyimpan absensi!', 400, ['error' => $e->getMessage()]);

        }
    }

    private function uploadBase64Image(string $base64Image, string $folder = 'images'): string
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            throw new \Exception('Format gambar tidak valid.');
        }

        $imageType = strtolower($type[1]);
        $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
        $base64Image = base64_decode($base64Image);

        if ($base64Image === false) {
            throw new \Exception('Base64 decode gagal.');
        }

        $filename = uniqid('proof_', true) . '.' . $imageType;
        $filePath = "{$folder}/{$filename}";

        Storage::disk('public')->put($filePath, $base64Image);

        return $filePath;
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
