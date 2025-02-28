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
            'file' => 'required|mimes:xlsx,csv',
        ]);

        Excel::import(new ScheduleImport, $request->file('file'));

        return redirect()->route('schedule.index')->with('success', 'Data berhasil diperbarui!');
    }
}
