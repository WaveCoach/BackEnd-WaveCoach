<?php

namespace App\Http\Controllers;

use App\Models\Coaches;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Package;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\ScheduleDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RescheduleRequestController extends Controller
{
    public function index(){
        $reschedules = RescheduleRequest::with('coach')->orderBy('id', 'desc')->get();
        // dd($reschedules);
        return view('pages.reschedule.index', compact('reschedules'));
    }

    public function edit($id){
        $reschedules = RescheduleRequest::with('coach')->orderBy('id', 'desc')->find($id);
        $scheduleId = $reschedules->schedule_id;
        $coaches = User::whereIn('role_id', [2, 3])->get(); // Ubah dari $coach ke $coaches
        $students = User::where('role_id', 4)->get();
        $locations = Location::all();
        $schedule = Schedule::with(['coach', 'students', 'location', 'package'])->findOrFail($scheduleId);
        $packages = Package::all();
        return view('pages.reschedule.edit', compact('reschedules', 'coaches', 'students', 'locations', 'schedule', 'packages'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
            'response_message' => 'nullable',
        ]);
        if($request->status == 'rejected'){
            $reschedules = RescheduleRequest::with('coach')->findOrFail($id);
            $reschedules->status = $request->status;
            $reschedules->response_message = $request->response_message;
            $reschedules->save();

            Notification::create([
                'pengirim_id'     => Auth::id(),
                'user_id'         => $reschedules->coach->id,
                'notifiable_id'   => $reschedules->id,
                'notifiable_type' => get_class($reschedules),
                'title'           => 'Reschedule Ditolak',
                'message'         => 'Permintaan reschedule Anda ditolak. Alasan: ' . $request->response_message,
                'is_read'         => 0,
                'type'            => 'reschedule',
            ]);
        }

        if($request->status == 'approved'){
            $reschedules = RescheduleRequest::with('coach')->findOrFail($id);
            $reschedules->status = $request->status;
            $reschedules->response_message = $request->response_message;
            $reschedules->save();

            $request->validate([
                'date' => 'required|date',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
                'coach_id' => 'nullable',
                'package_id' => 'required',
                'location_id' => 'nullable',
                'student_id' => 'required|array',
                'student_id.*' => 'required',
                'maps' => 'nullable',
                'email' => 'nullable',
                'is_assessed' => 'nullable'
            ]);

            $schedule = Schedule::findOrFail($reschedules->schedule_id);

            // Ambil data lama
            $oldDate = $schedule->date;
            $oldStart = $schedule->start_time;
            $oldEnd = $schedule->end_time;
            $oldLocationId = $schedule->location_id;

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

                Coaches::create([
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

            // Update schedule
            $schedule->update([
                'date' => $request->date ?? $schedule->date,
                'package_id' => $request->package_id ?? $schedule->package_id,
                'start_time' => $request->start_time ?? $schedule->start_time,
                'end_time' => $request->end_time ?? $schedule->end_time,
                'coach_id' => $coachId ?? $schedule->coach_id,
                'location_id' => $locationId ?? $schedule->location_id,
                'status' => 'rescheduled',
                'is_assessed' => $request->is_assessed,
            ]);

            // Cari perubahan
            $changes = [];
            if ($oldDate !== $request->date) {
                $changes[] = "Tanggal diubah dari *$oldDate* ke *{$request->date}*";
            }
            if ($oldStart !== $request->start_time || $oldEnd !== $request->end_time) {
                $changes[] = "Jam diubah dari *$oldStart - $oldEnd* ke *{$request->start_time} - {$request->end_time}*";
            }
            if ($oldLocationId != $locationId) {
                $oldLocationName = optional(Location::find($oldLocationId))->name ?? '-';
                $newLocationName = optional(Location::find($locationId))->name ?? '-';
                $changes[] = "Lokasi diubah dari *$oldLocationName* ke *$newLocationName*";
            }

            $changeMessage = count($changes) > 0 ? implode(", ", $changes) : "Tidak ada perubahan signifikan.";

            // Update student juga tetap
            $existingStudentIds = $schedule->students->pluck('id')->toArray();
            $newStudentIds = $request->student_id;

            ScheduleDetail::where('schedule_id', $schedule->id)
                ->whereNotIn('user_id', $newStudentIds)
                ->delete();

            foreach ($newStudentIds as $studentId) {
                if (!in_array($studentId, $existingStudentIds)) {
                    ScheduleDetail::create([
                        'user_id' => $studentId,
                        'schedule_id' => $schedule->id,
                    ]);
                }
            }

            // Buat notifikasi
            Notification::create([
                'pengirim_id'     => Auth::id(),
                'user_id'         => $reschedules->coach->id,
                'notifiable_id'   => $reschedules->id,
                'notifiable_type' => get_class($reschedules),
                'title'           => 'Reschedule Disetujui',
                'message'         => "Permintaan reschedule Anda disetujui. Perubahan: $changeMessage",
                'is_read'         => 0,
                'type'            => 'reschedule',
            ]);
        }


         // Redirect back with success message

        return redirect()->route('reschedule.index')
            ->with('success', 'permintaan reschedule berhasil diperbarui!');
    }
}
