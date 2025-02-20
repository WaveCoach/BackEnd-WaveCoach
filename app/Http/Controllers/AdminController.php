<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{

    public function index()
    {
        $admins = User::where('role_id', 1)->get();
        return view('pages.admin.index', compact('admins'));
    }


    public function create()
    {
        return view('pages.admin.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // dd($request->all());
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => 1,
        ]);
        return redirect()->route('admin.index')->with('success', 'admin berhasil ditambahkan dengan password: ');
    }


    public function show(string $id)
    {
        $admin = User::findOrFail($id);
        return view('pages.admin.show', compact('admin'));
    }


    public function edit(string $id)
    {
        $admin = User::findOrFail($id);
        return view('pages.admin.edit', compact('admin'));
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
        ]);

        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.index')->with('error', 'User not found');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.index')->with('success', 'User updated successfully');
    }


    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.index')->with('error', 'User not found');
        }

        $user->delete();
        return redirect()->route('admin.index')->with('success', 'User deleted successfully');
    }
}
