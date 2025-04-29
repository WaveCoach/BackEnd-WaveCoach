<?php

namespace App\Http\Controllers;

use App\Models\coaches;
use App\Models\location;
use App\Models\Notification;
use App\Models\Package;
use App\Models\PackageCoach;
use App\Models\PackageStudent;
use App\Models\schedule;
use App\Models\ScheduleDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Pusher\Pusher;

class ScheduleController extends Controller
{

    private function generateUniqueCode()
    {
        do {
            $code = random_int(1000, 9999);
        } while (Location::where('code_loc', $code)->exists());

        return $code;
    }

    public function index()
    {
        $schedules = Schedule::OrderBy('created_at', 'desc')->with(['coach', 'students'])->get();
        return view('pages.schedule.index', compact('schedules'));
    }

    public function store(Request $request) {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'package_id' => 'required',
            'coach_id' => 'nullable',
            'location_id' => 'nullable',
            'student_id' => 'required|array',
            'student_id.*' => 'required',
            'is_assessed' => 'nullable',
            'maps' => 'nullable',
            'email' => 'nullable'
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
                [
                    'address' => $request->address,
                    'maps' => $request->maps,
                    'code_loc' => $this->generateUniqueCode()
                ]
            );
            $locationId = $location->id;
        }

        $existingSchedule = Schedule::where('coach_id', $coachId)
            ->where('date', $request->date)
            ->where(function ($query) use ($request) {
            $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                  ->orWhere(function ($query) use ($request) {
                  $query->where('start_time', '<=', $request->start_time)
                    ->where('end_time', '>=', $request->end_time);
                  });
            })
            ->first();

        if ($existingSchedule) {
            return redirect()->back()->with('error', 'Coach ini sudah memiliki jadwal di rentang waktu tersebut.');
        }

        $schedule = Schedule::create([
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'package_id' => $request->package_id,
            'coach_id' => $coachId,
            'location_id' => $locationId,
            'status' => 'scheduled',
            'is_assessed' => $request->is_assessed ?? 0,
        ]);

        foreach ($request->student_id as $studentId) {
            ScheduleDetail::create([
                'user_id' => $studentId,
                'schedule_id' => $schedule->id,
            ]);
        }

        $coach = User::find($coachId);

        Notification::create([
            'pengirim_id'     => Auth   ::id(),
            'user_id'         => $coach->id,
            'notifiable_id'   => $schedule->id,
            'notifiable_type' => get_class($schedule),
            'title'           => 'Jadwal Baru',
            'message'         => 'Hai ' . $coach->name . ', jadwal baru Anda telah ditambahkan pada tanggal ' . $request->date . ' pukul ' . $request->start_time . ' - ' . $request->end_time,
            'is_read'         => false,
            'type'            => 'schedule',
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

        // Kirim event ke Pusher
        $pusher->trigger('notification-channel-user-' . $coach->id, 'NotificationSent', [
            'message' => 'Jadwal baru Anda telah ditambahkan pada tanggal ' . $request->date . ' pukul ' . $request->start_time . ' - ' . $request->end_time,
            'title'   => 'Jadwal Baru',
            'type'    => 'schedule',
        ]);


        return redirect()->route('schedule.index')->with('success', 'Schedule berhasil ditambahkan.');
    }

    public function create(){
        $coach = User::whereIn('role_id', [2, 3])->get();
        $students = User::where('role_id', 4)->get();
        $location = location::all();
        $packages = Package::all();
        return view('pages.schedule.create', compact('coach', 'students', 'location', 'packages'));
    }

    public function show($id)
    {
        $schedule = Schedule::with(['coach', 'students', 'location', 'package'])->findOrFail($id);
        return view('pages.schedule.show', compact('schedule'));
    }


    public function edit($id) {
        $coaches = User::whereIn('role_id', [2, 3])->get(); // Ubah dari $coach ke $coaches
        $students = User::where('role_id', 4)->get();
        $locations = Location::all();
        $schedule = Schedule::with(['coach', 'students', 'location', 'package'])->findOrFail($id);
        $packages = Package::all();

        return view('pages.schedule.edit', compact('coaches', 'students', 'locations', 'schedule', 'packages'));
    }

    public function getStudent(Request $request) {
        $coach = schedule::where('coach_id', $request->coach_id)->latest()->first();

        if (!$coach) {
            return response()->json(['error' => 'Coach tidak ditemukan'], 404);
        }

        $students = ScheduleDetail::with('user')->where('schedule_id', $coach->id)->get();

        return response()->json(['students' => $students]);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'coach_id' => 'nullable',
            'package_id' => 'required',
            'location_id' => 'nullable',
            'student_id' => 'required|array',
            'student_id.*' => 'required|integer|exists:users,id',
            'maps' => 'nullable',
            'email' => 'nullable',
            'is_assessed' => 'nullable'
        ]);

        $schedule = Schedule::findOrFail($id);

        // Proses untuk mengubah atau membuat coach
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

        // Proses untuk mengubah atau membuat lokasi
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

        // Simpan perubahan pada schedule
        $oldSchedule = $schedule->replicate(); // Menyimpan data jadwal sebelum diubah
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

        // Membuat pesan perubahan
        $changes = [];

        if ($schedule->date !== $oldSchedule->date) {
            $changes[] = "Tanggal berubah dari " . $oldSchedule->date . " menjadi " . $schedule->date;
        }
        if ($schedule->start_time !== $oldSchedule->start_time) {
            $changes[] = "Waktu mulai berubah dari " . $oldSchedule->start_time . " menjadi " . $schedule->start_time;
        }
        if ($schedule->end_time !== $oldSchedule->end_time) {
            $changes[] = "Waktu selesai berubah dari " . $oldSchedule->end_time . " menjadi " . $schedule->end_time;
        }
        if ($schedule->location_id !== $oldSchedule->location_id) {
            $oldLocation = Location::find($oldSchedule->location_id);
            $newLocation = Location::find($schedule->location_id);
            $changes[] = "Lokasi berubah dari " . $oldLocation->name . " menjadi " . $newLocation->name;
        }

        // Kirim notifikasi jika ada perubahan
        if (!empty($changes)) {
            $coach = User::find($coachId);

            Notification::create([
                'pengirim_id'     => Auth::id(),
                'user_id'         => $coach->id,
                'notifiable_id'   => $schedule->id,
                'notifiable_type' => get_class($schedule),
                'title'           => 'Jadwal Diperbarui',
                'message'         => 'Perubahan pada jadwal Anda: ' . implode(', ', $changes),
                'is_read'         => false,
                'type'            => 'schedule',
            ]);
        }

        // Update student
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

    public function createExcel(){
        return view('pages.schedule.import_schedule');
    }

    public function getStudentsByPackage($packageId)
    {
        $students = PackageStudent::where('package_id', $packageId)
        ->join('users', 'users.id', '=', 'package_student.student_id')
        ->select('users.id as user_id', 'users.name as student_name')
        ->get();

        $coach = PackageCoach::where('package_id', $packageId)
        ->join('users', 'users.id', '=', 'package_coach.coach_id')
        ->select('users.id as coach_id', 'users.name as coach_name')
        ->first();

        return response()->json([
            'students' => $students,
            'coach' => $coach
        ]);
    }

}
