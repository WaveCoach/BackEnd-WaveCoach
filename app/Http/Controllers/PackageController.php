<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $package = Package::orderBy('created_at', 'desc')->get();
        return view('pages.package.index', compact('package'));
    }

    public function create()
    {
        return view('pages.package.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string',
        ]);

        Package::create([
            'name' => $request->name,
            'desc' => $request->desc,
        ]);

        return redirect()->route('package.index')->with('success', 'Package created successfully.');
    }

    public function edit($id)
    {
        $package = Package::findOrFail($id);
        return view('pages.package.edit', compact('package'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string',
        ]);

        $package = Package::findOrFail($id);
        $package->update($request->all());

        return redirect()->route('package.index')->with('success', 'Package updated successfully.');
    }


}
