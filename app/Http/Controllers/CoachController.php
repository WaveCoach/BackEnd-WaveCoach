<?php

namespace App\Http\Controllers;

use App\Models\coaches;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CoachController extends Controller
{

    public function index()
    {
        $coaches = User::with('coach')->OrderBy('created_at', 'desc')->whereIn('role_id', [2, 3])->get();
        return view('pages.coach.index', compact('coaches'));
    }


    public function create()
    {
        return view('pages.coach.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|integer'
        ]);

        $randomPassword = Str::random(8);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($randomPassword),
            'role_id' => $request->role_id
        ]);

        Coaches::create([
            'user_id' => $user->id
        ]);

        return redirect()->route('coach.index')->with('success', 'Coach berhasil ditambahkan dengan password: ');
    }


    public function show(string $id)
    {
        $coach = User::findOrFail($id);
        return view('pages.coach.show', compact('coach'));
    }


    public function edit(string $id)
    {
        $coach = User::with('coach')->findOrFail($id);
        return view('pages.coach.edit', compact('coach'));
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'role_id' => 'required|integer'
        ]);

        $user = User::find($id);

        if (!$user) {
            return redirect()->route('coach.index')->with('error', 'User not found');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id
        ]);

        $coach = coaches::where('user_id', $id)->first();
        $coach-> status = $request->status;
        $coach->save();

        return redirect()->route('coach.index')->with('success', 'User updated successfully');
    }


    public function destroy($id)
    {
        $user = Coaches::where('user_id', $id)->first();

        if ($user) {
            $user->status = 'inactive';
            $user->save();
        }

        if (!$user) {
            return redirect()->route('coach.index')->with('error', 'User not found');
        }

        return redirect()->route('coach.index')->with('success', 'User deleted successfully');
    }
}
