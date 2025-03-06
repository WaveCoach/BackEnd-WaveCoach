<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CoachAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    public function store(Request $request)
    {
        // Validasi request
        $validated = $request->validate([
            'attendance_status' => 'required|string|in:Hadir,Tidak Hadir',
            'remarks' => 'nullable|string',
            'proof' => 'nullable|image|max:2048' // file gambar max 2MB
        ]);

        // Jika ada gambar yang diunggah
        if ($request->hasFile('proof')) {
            // Simpan file ke storage
            $path = $request->file('proof')->store('public/proofs');
            $validated['proof'] = Storage::url($path);
        }

        // Simpan data absensi ke database
        $attendance = CoachAttendance::create([
            'coach_id' => Auth::user()->id, // Asumsi coach login
            'attendance_status' => $validated['attendance_status'],
            'remarks' => $validated['remarks'] ?? null,
            'proof' => $validated['proof'] ?? null,
        ]);

        // Kembalikan response sukses
        return response()->json([
            'message' => 'Absensi berhasil disimpan',
            'attendance' => $attendance
        ], 201);
    }
}
