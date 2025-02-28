<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcement = Announcement::with('users')->latest()->get();
        return view('pages.announcement.index', compact('announcement'));
    }

    public function create()
    {
        $users = User::whereIn('role_id', [2, 3])->get();
        // dd($users);
        return view('pages.announcement.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'student_id' => 'required|array',  // Pastikan student_id dipilih
            'student_id.*' => 'exists:users,id', // Validasi id pengguna yang dipilih
        ]);

        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'published_at' => $request->published_at,
            'image' => $request->file('image') ? $request->file('image')->store('public/images') : null,  // Menyimpan gambar jika ada
        ]);

        $announcement->users()->attach($request->student_id);

        return redirect()->route('announcement.index')->with('success', 'Pengumuman berhasil dibuat');
    }

    public function show(Announcement $announcement)
    {
        return view('pages.announcement.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        $users = User::all();
        $announcementUser = AnnouncementUser::where('announcement_id', $announcement->id)->get();
        return view('pages.announcement.edit', compact('announcement', 'users'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        // Validasi input
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $data = $request->except('user_ids');

        if ($request->hasFile('image')) {
            if ($announcement->image) {
                Storage::delete($announcement->image);
            }

            $data['image'] = $request->file('image')->store('announcements', 'public');
        }

        $announcement->update($data);

        if ($request->has('user_ids')) {
            $announcement->users()->sync($request->user_ids);
        }

        return redirect()->route('announcements.index')->with('success', 'Announcement updated!');
    }


    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('announcement.index')->with('success', 'Announcement deleted!');
    }
}
