<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{

    public function index()
    {
        $students = User::where('role_id', 4)->get();
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
        ]);

        $randomPassword = Str::random(8);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($randomPassword),
            'role_id' => 4,
        ]);
        return redirect()->route('student.index')->with('success', 'student berhasil ditambahkan dengan password: ');
    }


    public function show(string $id)
    {
        $student = User::findOrFail($id);
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
        ]);

        $user = User::find($id);

        if (!$user) {
            return redirect()->route('student.index')->with('error', 'User not found');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('student.index')->with('success', 'User updated successfully');
    }


    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('student.index')->with('error', 'User not found');
        }

        $user->delete();
        return redirect()->route('student.index')->with('success', 'User deleted successfully');
    }
}
