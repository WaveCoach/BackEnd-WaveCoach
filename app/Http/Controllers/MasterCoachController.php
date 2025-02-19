<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MasterCoachController extends Controller
{

    public function index()
    {
        $mastercoaches = User::where('role_id', 3)->get();
        return view('pages.mastercoach.index', compact('mastercoaches'));
    }


    public function create()
    {
        return view('pages.mastercoach.create');
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
            'role_id' => 3,
        ]);

        return redirect()->route('mastercoach.index')->with('success', 'Master Coach berhasil ditambahkan dengan password: ');
    }


    public function show(string $id)
    {
        $mastercoach = User::findOrFail($id);
        return view('pages.mastercoach.show', compact('mastercoach'));
    }


    public function edit(string $id)
    {
        $mastercoach = User::findOrFail($id);
        return view('pages.mastercoach.edit', compact('mastercoach'));
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
        ]);

        $user = User::find($id);

        if (!$user) {
            return redirect()->route('mastercoach.index')->with('error', 'User not found');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('mastercoach.index')->with('success', 'User updated successfully');
    }


    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('mastercoach.index')->with('error', 'User not found');
        }

        $user->delete();
        return redirect()->route('mastercoach.index')->with('success', 'User deleted successfully');
    }
}
