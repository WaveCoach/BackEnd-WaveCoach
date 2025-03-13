<?php

namespace App\Http\Controllers;

use App\Models\AssessmentAspect;
use App\Models\AssessmentCategory;
use Illuminate\Http\Request;

class AssesmentAspectController extends Controller
{

    public function index()
    {
        $categories = AssessmentCategory::OrderBy('created_at', 'desc')->with('aspects')->get();
        return view('pages.assesment_aspect.index', compact('categories'));
    }



    public function create()
    {
        $categories = AssessmentCategory::get();
        return view('pages.assesment_aspect.create', compact('categories'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'assessment_categories_id' => 'required',
            'name' => 'required|array|min:1',
            'name.*' => 'string|max:255'
        ]);

        // Cek apakah ID kategori valid atau harus dibuat baru
        if (is_numeric($request->assessment_categories_id)) {
            $categoryId = $request->assessment_categories_id;
        } else {
            $category = AssessmentCategory::firstOrCreate(['name' => $request->assessment_categories_id]);
            $categoryId = $category->id;
        }

        $names = json_decode($request->name[0], true);

        if (!is_array($names)) {
            return back()->withErrors(['name' => 'Format data tidak valid.']);
        }

        $existingAspects = AssessmentAspect::where('assessment_categories_id', $categoryId)
            ->pluck('name')
            ->toArray();

        $newAspects = [];
        foreach ($names as $item) {
            $name = is_array($item) && isset($item['value']) ? $item['value'] : $item;

            if (in_array($name, $existingAspects)) {
                return back()->withErrors(['name' => "Aspek '{$name}' sudah ada dalam kategori ini."]);
            }

            $newAspects[] = [
                'assessment_categories_id' => $categoryId,
                'name' => $name
            ];
        }

        AssessmentAspect::insert($newAspects);

        return redirect()->route('assesment-aspect.index')->with('success', 'Aspek Penilaian berhasil ditambahkan');
    }


    public function show(string $id)
    {
        // $aspect = AssessmentAspect::findOrFail($id);
        $category = AssessmentCategory::with('aspects')->findOrFail($id);
        return view('pages.assesment_aspect.show', compact('category'));
    }

    public function edit(string $id)
    {
        $aspect = AssessmentAspect::findOrFail($id);
        return view('pages.assesment_aspect.edit', compact('aspect'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $aspect = AssessmentAspect::findOrFail($id);

        $existingAspect = AssessmentAspect::where('assessment_categories_id', $aspect->assessment_categories_id)
            ->where('name', $request->name)
            ->where('id', '!=', $id)
            ->exists();

        if ($existingAspect) {
            return back()->withErrors(['name' => 'Aspek dengan nama ini sudah ada dalam kategori yang sama.']);
        }

        $aspect->update([
            'name' => $request->name,
        ]);

        return redirect()->route('assesment-aspect.index')->with('success', 'Aspek berhasil diperbarui.');
    }



    public function destroy(string $id)
    {
        $aspect = AssessmentAspect::find($id);

        if (!$aspect) {
            return redirect()->route('assesment-aspect.index')->with('error', 'Aspek tidak ditemukan');
        }

        $aspect->delete();
        return redirect()->route('assesment-aspect.index')->with('success', 'Aspek berhasil dihapus');
    }

    public function asessmentedit($id){
        $categories = AssessmentCategory::all(); // Ambil semua kategori untuk dropdown
        $selectedCategory = AssessmentCategory::find($id); // Kategori yang sedang diedit
        $aspects = AssessmentAspect::where('assessment_categories_id', $id)->get(); // Aspek yang terkait

        return view('pages.assesment_aspect.aspectedit', compact('categories', 'selectedCategory', 'aspects'));
    }

    public function asessmentupdate(Request $request, $id){
        $request->validate([
            'assessment_categories_id' => 'required',
            'name' => 'required|array|min:1',
            'name.*' => 'required|string|max:255'
        ]);

        $assesmentAspects = AssessmentAspect::where('assessment_categories_id', $id)->get();

        if (is_numeric($request->assessment_categories_id)) {
            $categoryId = $request->assessment_categories_id;
        } else {
            $category = AssessmentCategory::firstOrCreate(['name' => $request->assessment_categories_id]);
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
        AssessmentAspect::where('assessment_categories_id', $id)
            ->whereIn('name', $namesToDelete)
            ->delete();

        foreach ($names as $name) {
            AssessmentAspect::updateOrCreate(
                ['assessment_categories_id' => $categoryId, 'name' => $name],
                ['assessment_categories_id' => $categoryId, 'name' => $name]
            );
        }

        return redirect()->route('assesment-aspect.index')->with('success', 'Aspek Penilaian berhasil diperbarui');
    }
}
