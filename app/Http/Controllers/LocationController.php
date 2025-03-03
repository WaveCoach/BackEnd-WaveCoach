<?php

namespace App\Http\Controllers;

use App\Exports\LocationExport;
use App\Imports\LocationImport;
use App\Models\location;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

        do {
            $code = random_int(1000, 9999);
        } while (Location::where('code_loc', $code)->exists());

        Location::create([
            'name' => $request->name,
            'address' => $request->address,
            'maps' => $request->maps,
            'code_loc' => $code
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

    public function exportLocations(): BinaryFileResponse
    {
        return Excel::download(new LocationExport, 'Location.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        Excel::import(new LocationImport, $request->file('file'));

        return redirect()->route('location.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function importCreate(){
        return view('pages.location.import-location');
    }
}
