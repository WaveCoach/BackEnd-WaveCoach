<?php

namespace App\Http\Controllers\API;

use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\ScheduleDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends BaseController
{
    public function getSchedule(Request $request) {
        $schedule = Schedule::with(['coach', 'location'])->where('coach_id', Auth::user()->id);

        if ($request->day) {
            $daysMap = [
                'minggu' => 1,
                'senin' => 2,
                'selasa' => 3,
                'rabu' => 4,
                'kamis' => 5,
                'jumat' => 6,
                'sabtu' => 7
            ];

            $dayNumber = $daysMap[strtolower($request->day)] ?? null;

            if ($dayNumber) {
                $schedule->whereRaw('DAYOFWEEK(date) = ?', [$dayNumber]);
            }
        }

        // Ambil data dan format tanggal
        $schedule = $schedule->get()->map(function ($item) {
            $date = Carbon::parse($item->date)->locale('id');

            $item->formatted_date = $date->translatedFormat('l, d F Y'); // Senin, 20 Agustus 2025
            $item->day_number = $date->translatedFormat('d'); // 20
            $item->month = $date->translatedFormat('F'); // Agustus
            $item->year = $date->translatedFormat('Y'); // 2025

            return $item;
        });

        return $this->SuccessResponse(['schedule' => $schedule], 'Schedule retrieved successfully');
    }


    public function getDetailSchedule($id){
        $schedule = Schedule::with(['coach', 'location'])->find($id);

        if ($schedule) {
            $date = Carbon::parse($schedule->date)->locale('id');
            $schedule->date = $date->translatedFormat('l, d F Y');
            $schedule->day_number = $date->translatedFormat('d'); // 20
            $schedule->month = $date->translatedFormat('F'); // Agustus
            $schedule->year = $date->translatedFormat('Y'); // 2025
        }

        $student = ScheduleDetail::with('student')->where('schedule_id', $id)->get();

        return $this->SuccessResponse(['schedule' => $schedule, 'student' => $student], 'Schedule retrieved successfully');
    }


    public function requestReschedule(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'reason' => 'required|string',
        ]);

        $rescheduleRequest = RescheduleRequest::create([
            'schedule_id' => $validated['schedule_id'],
            'coach_id' => Auth::user()->id,
            'reason' => $validated['reason'],
            'status' => $validated['status'] ?? 'pending',
            'admin_id' => null,
            'response_message' => null,
        ]);

        return $this->SuccessResponse($rescheduleRequest, 'Permintaan reschedule berhasil dikirim', 201);
    }



}
