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
        return view('pages.announcement.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'user_id' => 'required|array',
            'user_id.*' => ['nullable', function ($attribute, $value, $fail) {
                if (!in_array($value, ['semua', 'mastercoach', 'coach']) && !is_numeric($value)) {
                    $fail("Pilihan $value tidak valid.");
                }
            }],
        ]);

        $selectedIds = array_filter($request->user_id, fn($id) => is_numeric($id));

        $userIds = $selectedIds;

        if (in_array('semua', $request->user_id)) {
            $userIds = array_merge($userIds, User::whereIn('role_id', [2, 3])->pluck('id')->toArray());
        }
        if (in_array('mastercoach', $request->user_id)) {
            $userIds = array_merge($userIds, User::where('role_id', 3)->pluck('id')->toArray());
        }
        if (in_array('coach', $request->user_id)) {
            $userIds = array_merge($userIds, User::where('role_id', 2)->pluck('id')->toArray());
        }

        $userIds = array_unique($userIds);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'published_at' => $request->published_at,
            'image' => $imagePath,
        ]);

        $announcement->users()->attach($userIds);

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
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'student_id' => 'required|array',
            'student_id.*' => 'exists:users,id',
        ]);

        $data = $request->except('student_id');

        if ($request->hasFile('image')) {
            if ($announcement->image) {
                Storage::delete($announcement->image);
            }

            $data['image'] = $request->file('image')->store('images', 'public');
        }

        $announcement->update($data);

        if ($request->has('student_id')) {
            $announcement->users()->sync($request->student_id);  // Sync hanya user yang ada dalam 'student_id'
        }

        return redirect()->route('announcement.index')->with('success', 'Pengumuman berhasil diupdate!');
    }




    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('announcement.index')->with('success', 'Announcement deleted!');
    }
}
