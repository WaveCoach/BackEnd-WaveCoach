<?php

namespace App\Http\Controllers;

use App\Imports\ScheduleImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ScheduleImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:2048', // Maksimal 2MB
        ], [
            'file.required' => 'File harus diunggah!',
            'file.mimes' => 'Format file harus .xlsx atau .csv!',
            'file.max' => 'Ukuran file tidak boleh lebih dari 2MB!',
        ]);

        try {
            Excel::import(new ScheduleImport, $request->file('file'));
            return redirect()->route('schedule.index')->with('success', 'Data berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Terjadi kesalahan saat mengimpor file. Pastikan format sesuai!']);
        }
    }
}
