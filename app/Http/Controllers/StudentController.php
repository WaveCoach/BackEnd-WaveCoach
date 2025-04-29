<?php

namespace App\Http\Controllers;

use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use App\Models\Package;
use App\Models\PackageStudent;
use App\Models\ScheduleDetail;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{

    public function index()
    {
        $students = User::with('student')->Orderby('created_at', 'desc')->where('role_id', 4)->get();
        return view('pages.student.index', compact('students'));
    }

    public function create()
    {
        $packages = Package::all();
        return view('pages.student.create', compact('packages'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'email_parent' => 'nullable|email|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'tanggal_bergabung' => 'required|date',
            'package_id' => 'nullable|array',
            'package_id.*' => 'exists:packages,id',
        ]);

        $tahunMasuk = now()->format('y');
        $lastStudent = Student::where('nis', 'like', $tahunMasuk . '%')->orderBy('nis', 'desc')->first();
        $nextNumber = $lastStudent ? ((int)substr($lastStudent->nis, 2) + 1) : 1;
        $nis = $tahunMasuk . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $user = User::create([
            'name' => $request->name,
            'email_parent' => $request->email_parent,
            'password' => bcrypt('12345678'),
            'role_id' => 4
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'tanggal_bergabung' => $request->tanggal_bergabung,
            'nis' => $nis
        ]);

        // dd($student);

        if ($request->filled('package_id')) {
            foreach ($request->package_id as $packageId) {
                PackageStudent::create([
                    'student_id' => $user->id,
                    'package_id' => $packageId
                ]);
            }
        }

        return redirect()->route('student.index')->with('success', 'Student berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $student = User::with('student')->findOrFail($id);
        $package = PackageStudent::where('student_id', $id)->with('package')->get();
        return view('pages.student.show', compact('student', 'package'));
    }

    public function edit(string $id)
    {
        $student = User::findOrFail($id);
        $packageSelected = PackageStudent::where('student_id', $id)->pluck('package_id')->toArray(); // ambil ID aja
        $allPackages = Package::all();

        return view('pages.student.edit', compact('student', 'packageSelected', 'allPackages'));
    }

    public function update(Request $request, string $id)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'email_parent' => 'nullable|email|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'tanggal_bergabung' => 'required|date',
            'type' => 'nullable|string|max:50',
            'package_id' => 'nullable|array',
            'package_id.*' => 'exists:packages,id',
        ]);

        $user = User::find($id);

        if (!$user) {
            return redirect()->route('student.index')->with('error', 'User not found');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'email_parent' => $request->email_parent,
        ]);

        $student = Student::where('user_id', $id)->first();
        $student->jenis_kelamin = $request->jenis_kelamin;
        $student->tanggal_lahir = $request->tanggal_lahir;
        $student->tanggal_bergabung = $request->tanggal_bergabung;
        $student->type = $request->type;
        $student->save();

        PackageStudent::where('student_id', $user->id)->delete();

        if ($request->filled('package_id')) {
            foreach ($request->package_id as $packageId) {
                PackageStudent::create([
                    'student_id' => $user->id,
                    'package_id' => $packageId
                ]);
            }
        }

        return redirect()->route('student.index')->with('success', 'User updated successfully');
    }

    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('student.index')->with('error', 'User tidak ditemukan');
        }

        $existsInSchedule = ScheduleDetail::where('user_id', $id)->exists();

        if ($existsInSchedule) {
            return redirect()->route('student.index')->with('error', 'User tidak dapat dihapus karena masih terhubung dengan Schedule!');
        }

        $user->delete();
        return redirect()->route('student.index')->with('success', 'User berhasil dihapus');
    }

    public function export()
    {
        return Excel::download(new StudentsExport, 'students.xlsx');
    }

    public function formImport()
    {
        return view('pages.student.import');
    }

    public function Import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new StudentsImport, $request->file('file'));

        return redirect()->back()->with('success', 'Import berhasil!');
    }

}
