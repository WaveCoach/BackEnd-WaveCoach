<?php

namespace App\Http\Controllers;

use App\Models\assesment_aspect;
use App\Models\assesment_category;
use Illuminate\Http\Request;

class AssesmentAspectController extends Controller
{

    public function index()
    {
        $categories = assesment_category::OrderBy('created_at', 'desc')->with('aspects')->get();
        return view('pages.assesment_aspect.index', compact('categories'));
    }



    public function create()
    {
        $categories = assesment_category::get();
        return view('pages.assesment_aspect.create', compact('categories'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'assesment_categories_id' => 'required',
            'name' => 'required|array|min:1',
            'name.*' => 'string|max:255'
        ]);

        if (is_numeric($request->assesment_categories_id)) {
            $categoryId = $request->assesment_categories_id;
        } else {
            $category = assesment_category::firstOrCreate(['name' => $request->assesment_categories_id]);
            $categoryId = $category->id;
        }

        $names = json_decode($request->name[0], true);

        if (!is_array($names)) {
            return back()->withErrors(['name' => 'Format data tidak valid.']);
        }

        foreach ($names as $item) {
            $name = is_array($item) && isset($item['value']) ? $item['value'] : $item;

            assesment_aspect::create([
                'assesment_categories_id' => $categoryId,
                'name' => $name
            ]);
        }

        return redirect()->route('assesment-aspect.index')->with('success', 'Aspek Penilaian berhasil ditambahkan');
    }

    public function show(string $id)
    {
        // $aspect = assesment_aspect::findOrFail($id);
        $category = assesment_category::with('aspects')->findOrFail($id);
        return view('pages.assesment_aspect.show', compact('category'));
    }

    public function edit(string $id)
    {
        $aspect = assesment_aspect::findOrFail($id);
        return view('pages.assesment_aspect.edit', compact('aspect'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $aspect = assesment_aspect::findOrFail($id);

        $aspect->update([
            'name' => $request->name,
        ]);

        return redirect()->route('assesment-aspect.index')->with('success', 'Aspek berhasil diperbarui.');
    }



    public function destroy(string $id)
    {
        $aspect = assesment_aspect::find($id);

        if (!$aspect) {
            return redirect()->route('assesment-aspect.index')->with('error', 'Aspek tidak ditemukan');
        }

        $aspect->delete();
        return redirect()->route('assesment-aspect.index')->with('success', 'Aspek berhasil dihapus');
    }

    public function asessmentedit($id){
        $categories = assesment_category::all(); // Ambil semua kategori untuk dropdown
        $selectedCategory = assesment_category::find($id); // Kategori yang sedang diedit
        $aspects = assesment_aspect::where('assesment_categories_id', $id)->get(); // Aspek yang terkait

        return view('pages.assesment_aspect.aspectedit', compact('categories', 'selectedCategory', 'aspects'));
    }

    public function asessmentupdate(Request $request, $id){
        $request->validate([
            'assesment_categories_id' => 'required',
            'name' => 'required|array|min:1',
            'name.*' => 'required|string|max:255'
        ]);

        $assesmentAspects = assesment_aspect::where('assesment_categories_id', $id)->get();

        if (is_numeric($request->assesment_categories_id)) {
            $categoryId = $request->assesment_categories_id;
        } else {
            $category = assesment_category::firstOrCreate(['name' => $request->assesment_categories_id]);
            $categoryId = $category->id;
        }

        $decodedNames = json_decode($request->name[0], true);

        if (!is_array($decodedNames)) {
            return back()->withErrors(['name' => 'Format data tidak valid.']);
        }

        $names = array_map(fn($item) => $item['value'] ?? '', $decodedNames);
        $names = array_filter($names);

        if (empty($names)) {
            return back()->withErrors(['name' => 'Harus ada setidaknya satu aspek penilaian.']);
        }

        $existingNames = $assesmentAspects->pluck('name')->toArray();
        $namesToDelete = array_diff($existingNames, $names);
        assesment_aspect::where('assesment_categories_id', $id)
            ->whereIn('name', $namesToDelete)
            ->delete();

        foreach ($names as $name) {
            assesment_aspect::updateOrCreate(
                ['assesment_categories_id' => $categoryId, 'name' => $name],
                ['assesment_categories_id' => $categoryId, 'name' => $name]
            );
        }

        return redirect()->route('assesment-aspect.index')->with('success', 'Aspek Penilaian berhasil diperbarui');
    }
}
