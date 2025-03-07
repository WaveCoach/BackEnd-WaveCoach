<?php

namespace App\Http\Controllers;

use App\Models\ScheduleDetail;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{

    public function index()
    {
        $students = User::with('student')->Orderby('created_at', 'desc')->where('role_id', 4)->get();
        return view('pages.student.index', compact('students'));
    }


    public function create()
    {
        return view('pages.student.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'jenis_kelamin' => 'required|in:L,P', // L = Laki-laki, P = Perempuan
            'tanggal_lahir' => 'required|date',
            'type' => 'nullable|string|max:50',

        ]);

        $tahunMasuk = Carbon::now()->format('y');
        $lastStudent = Student::where('nis', 'like', $tahunMasuk . '%')->orderBy('nis', 'desc')->first();
        $nextNumber = $lastStudent ? ((int)substr($lastStudent->nis, 2) + 1) : 1;
        $nis = $tahunMasuk . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt('12345678'),
            'role_id' => 4
        ]);

        Student::create([
            'user_id' => $user->id,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'type' => $request->type,
            'nis' => $nis
        ]);

        return redirect()->route('student.index')->with('success', 'student berhasil ditambahkan dengan password: ');
    }


    public function show(string $id)
    {
        $student = User::with('student')->findOrFail($id);
        return view('pages.student.show', compact('student'));
    }


    public function edit(string $id)
    {
        $student = User::findOrFail($id);
        return view('pages.student.edit', compact('student'));
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'jenis_kelamin' => 'required|in:L,P', // L = Laki-laki, P = Perempuan
            'tanggal_lahir' => 'required|date',
            'type' => 'nullable|string|max:50',
        ]);

        $user = User::find($id);

        if (!$user) {
            return redirect()->route('student.index')->with('error', 'User not found');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $student = Student::where('user_id', $id)->first();
        $student -> jenis_kelamin = $request->jenis_kelamin;
        $student->tanggal_lahir = $request->tanggal_lahir;
        $student->type = $request->type;
        $student->save();


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
}
