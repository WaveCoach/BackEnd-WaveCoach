<?php

namespace App\Http\Controllers\API;

use App\Models\CoachAttendance;
use App\Models\Notification;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\ScheduleDetail;
use App\Models\StudentAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends BaseController
{
    public function getSchedule(Request $request) {
        $schedule = Schedule::with(['coach', 'location', 'package'])->where('coach_id', Auth::user()->id)->OrderBy('date', 'asc');

        if ($request->has('history')) {
            $schedule->where('date', '<', Carbon::today()->toDateString()); // Pakai Carbon langsung
        }
        elseif ($request->month) {
            $monthsMap = [
                'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
                'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
                'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12
            ];

            $monthNumber = is_numeric($request->month) ? (int) $request->month : ($monthsMap[strtolower($request->month)] ?? null);

            if ($monthNumber) {
                $schedule->whereRaw('MONTH(date) = ?', [$monthNumber]);
            }
        }
        elseif ($request->has('date')) {
            $inputDate = Carbon::parse($request->date)->toDateString();
            $schedule->where('date', $inputDate);
        }
        else {
            $schedule->where('date', '>=', Carbon::today()->subMonth()->toDateString());
        }

        $schedule = $schedule->get()->map(function ($item) {
            $date = Carbon::parse($item->date)->locale('id');

            return [
                'id' => $item->id,
                'date' => $item->date,
                'start_time' => $item->start_time,
                'end_time' => $item->end_time,
                'status' => $item->status,
                'formatted_date' => $date->translatedFormat('l, d F Y'),
                'coach_name' => $item->coach->name,
                'location_name' => $item->location->name,
                'location_address' => $item->location->address,
                'location_maps' => $item->location->maps,
                'package_id' => $item->package->id ?? null,
                'status' => $item->status,
                'package_name' => $item->package->name ?? null,
                'is_assessed' => $item->is_assessed,
            ];
        });

        return $this->SuccessResponse(['schedule' => $schedule], 'Schedule retrieved successfully');
    }

    public function getDetailSchedule($id)
    {
        $schedule = Schedule::with(['coach', 'location', 'package', 'rescheduleRequests'])->find($id);


        if (!$schedule) {
            return $this->ErrorResponse('Schedule not found', 404);
        }

        $date = Carbon::parse($schedule->date)->locale('id');

        $formattedSchedule = [
            'id' => $schedule->id,
            'date' => $schedule->date,
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
            'status' => $schedule->status,
            'formatted_date' => $date->translatedFormat('l, d F Y'),
            'package_id' => $schedule->package_id ?? null,
            'package_name' => $schedule->package->name ?? null,
            'status' => $schedule->status,
            'has_reschedule_request' => $schedule->rescheduleRequests && $schedule->rescheduleRequests->isNotEmpty(),
            'is_assessed' => $schedule->is_assessed,


        ];

        $location = [
            'name' => $schedule->location->name,
            'address' => $schedule->location->address,
            'maps' => $schedule->location->maps,
        ];

        $coachAttendance = CoachAttendance::where('schedule_id', $id)
            ->where('coach_id', $schedule->coach->id)
            ->first();

        $coach = [
            'id' => $schedule->coach->id,
            'name' => $schedule->coach->name,
            'attendance_status' => $coachAttendance->attendance_status ?? null,
        ];

        $students = ScheduleDetail::with('student')
            ->where('schedule_id', $id)
            ->get()
            ->map(function ($item) use ($id) {
                $attendance = StudentAttendance::where('schedule_id', $id)
                    ->where('student_id', $item->student->id)
                    ->first();

                return [
                    'id' => $item->student->id,
                    'name' => $item->student->name,
                    'attendance_status' => $attendance->attendance_status ?? null,
                ];
            });

        return $this->SuccessResponse([
            'schedule' => $formattedSchedule,
            'location' => $location,
            'coach' => $coach,
            'students' => $students,
        ], 'Schedule retrieved successfully');
    }

        public function requestReschedule(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'reason' => 'required|string',
        ]);

        $existingRequest = RescheduleRequest::where('schedule_id', $validated['schedule_id'])
            ->where('coach_id', Auth::user()->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingRequest) {
            return $this->ErrorResponse('Permintaan reschedule sudah ada dan masih dalam proses.', 400);
        }

        $rescheduleRequest = RescheduleRequest::create([
            'schedule_id' => $validated['schedule_id'],
            'coach_id' => Auth::user()->id,
            'reason' => $validated['reason'],
            'status' => $validated['status'] ?? 'pending',
            'admin_id' => null,
            'response_message' => null,
        ]);

        $admins = User::where('role_id', 1)->get(); // Ambil semua admin

        $pusher = new \Pusher\Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ]
        );

        foreach ($admins as $admin) {
            // Simpan notifikasi di database
            Notification::create([
                'pengirim_id'     => Auth::id(),
                'user_id'         => $admin->id,
                'notifiable_id'   => $rescheduleRequest->id,
                'notifiable_type' => get_class($rescheduleRequest),
                'title'           => 'Permintaan Reschedule Baru',
                'message'         => Auth::user()->name . ' mengajukan permintaan reschedule.',
                'is_read'         => 0,
                'type'            => 'reschedule',
            ]);

            // Kirim Notif Real-time via Pusher ke channel spesifik berdasarkan userId
            $pusher->trigger('notification-channel-user-' . $admin->id, 'NotificationSent', [
                'message' => Auth::user()->name . ' mengajukan permintaan reschedule.',
                'title'   => 'Permintaan Reschedule Baru',
                'type'    => 'reschedule',
            ]);
        }

        return $this->SuccessResponse($rescheduleRequest, 'Permintaan reschedule berhasil dikirim', 201);
    }






}
