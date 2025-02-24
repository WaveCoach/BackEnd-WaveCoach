<?php

namespace App\Http\Controllers;

use App\Models\location;
use Illuminate\Http\Request;

class LocationController extends Controller
{

    public function index()
    {
        $locations = Location::orderBy('created_at', 'desc')->get();
        return view('pages.location.index', compact('locations'));
    }


    public function create()
    {
        return view('pages.location.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'maps'=> 'required|url'
        ]);

        location::create([
            'name' => $request->name,
            'address' => $request->address,
            'maps' => $request->maps
        ]);

        return redirect()->route('location.index')->with('success', 'Data berhasil disimpan!');
    }


    public function show(string $id)
    {
        $location = Location::findOrFail($id);
        return view('pages.location.show', compact('location'));
    }


    public function edit(string $id)
    {
        $location = Location::findOrFail($id);
        return view('pages.location.edit', compact('location'));
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
             'maps'=> 'required|url'
        ]);

        $location = Location::findOrFail($id);
        $location->update([
            'name' => $request->name,
            'address' => $request->address,
            'maps' => $request->maps
        ]);

        return redirect()->route('location.index')->with('success', 'Data berhasil diperbarui!');
    }


    public function destroy($id)
    {

        $location = Location::findOrFail($id);
        $location->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }
}
