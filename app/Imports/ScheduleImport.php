<?php

namespace App\Imports;

use App\Models\Location;
use App\Models\Schedule;
use App\Models\schedule_detail;
use App\Models\ScheduleDetail;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ScheduleImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $coach = User::where('email', $row[0])->first();
        if (!$coach) {
            return null;
        }

        $location = Location::where('code_loc', $row[1])->first();
        if (!$location) {
            return null;
        }

        // Konversi angka serial Excel ke format Y-m-d
        $date = is_numeric($row[2])
            ? Carbon::createFromFormat('Y-m-d', gmdate("Y-m-d", ($row[2] - 25569) * 86400))
            : Carbon::parse($row[2]);

            $startTime = Carbon::parse(Date::excelToDateTimeObject($row[3]), 'Asia/Jakarta')->format('H:i:s');
            $endTime = Carbon::parse(Date::excelToDateTimeObject($row[4]), 'Asia/Jakarta')->format('H:i:s');
        // dd(Date::excelToDateTimeObject($row[3]));

        $schedule = Schedule::create([
            'coach_id'   => $coach->id,
            'location_id' => $location->id,
            'date'       => $date->format('Y-m-d'), // Format ke Y-m-d
            'start_time' => $startTime,
            'end_time'   => $endTime,
        ]);

        $userEmails = explode(',', $row[5]);
        foreach ($userEmails as $email) {
            $email = trim($email);
            $user = User::where('email', $email)->first();
            if ($user) {
                schedule_detail::create([
                    'schedule_id' => $schedule->id,
                    'user_id' => $user->id,
                ]);
            }
        }

        return $schedule;
    }

    public function rules(): array
    {
        return [
            '*.coach_email' => 'required|email|exists:users,email',
            '*.location'    => 'required|exists:locations,name',
            '*.date'        => 'required',
            '*.start_time'  => 'required|date_format:H:i',
            '*.end_time'    => 'required|date_format:H:i',
            '*.user_emails' => 'required|string',
        ];
    }

    public function startRow(): int
    {
        return 2;
    }
}
