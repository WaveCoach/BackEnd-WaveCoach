<?php

namespace App\Http\Controllers\API;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends BaseController
{
    public function getSchedule(Request $request){
        $schedule = Schedule::where('coach_id', Auth::user()->id);

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

        $schedule = $schedule->get();

        return $this->SuccessResponse(['schedule' => $schedule], 'Schedule retrieved successfully');
    }



}
