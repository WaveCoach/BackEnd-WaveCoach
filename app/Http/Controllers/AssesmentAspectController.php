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
            'name.*' => 'string|max:255',
            'kkm' => 'nullable|numeric',
            'description' => 'required|array|min:1',
            'description.*' => 'string|max:255',
        ]);

        if (is_numeric($request->assessment_categories_id)) {
            $categoryId = $request->assessment_categories_id;
        } else {
            $category = AssessmentCategory::firstOrCreate([
                'name' => $request->assessment_categories_id,
                'kkm' => $request->kkm,
            ]);
            $categoryId = $category->id;
        }


        $names = $request->input('name');
        $descriptions = $request->input('description');

        $existingAspects = AssessmentAspect::where('assessment_categories_id', $categoryId)
        ->pluck('name')
        ->toArray();

        $newAspects = [];
        foreach ($names as $index => $name) {
            $description = $descriptions[$index] ?? '';

            if (in_array($name, $existingAspects)) {
                return back()->withErrors(['name' => "Aspek '{$name}' sudah ada dalam kategori ini."]);
            }

            $newAspects[] = [
                'assessment_categories_id' => $categoryId,
                'name' => $name,
                'desc' => $description,
            ];
        }

        AssessmentAspect::insert($newAspects);


        return redirect()->route('assesment-aspect.index')->with('success', 'Aspek Penilaian berhasil ditambahkan');
    }


    public function show(string $id)
    {
        $aspect = AssessmentAspect::where('assessment_categories_id', $id)->get(); //didalam sini ada kolom aspek dan desc
        $categories = AssessmentCategory::with('aspects')->findOrFail($id); // didalam sini ada kolom name dan kkm
        $allCategories = AssessmentCategory::all();
        return view('pages.assesment_aspect.show', compact('categories'));
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
        $aspect = AssessmentAspect::where('assessment_categories_id', $id)->get(); //didalam sini ada kolom aspek dan desc
        $categories = AssessmentCategory::with('aspects')->findOrFail($id); // didalam sini ada kolom name dan kkm
        $allCategories = AssessmentCategory::all();
        return view('pages.assesment_aspect.aspectedit', compact('categories', 'aspect', 'allCategories'));
    }

    public function asessmentupdate(Request $request, $id)
    {
        $request->validate([
            'assessment_categories_id' => 'required|integer',
            'kkm' => 'required|numeric|min:0|max:100',
            'name' => 'required|array|min:1',
            'name.*' => 'required',
            'description' => 'required|array|min:1',
            'description.*' => 'required|string|max:1000',
        ]);

        $categoryId = $request->assessment_categories_id;

        AssessmentCategory::where('id', $categoryId)->update([
            'kkm' => $request->kkm,
        ]);

        $existingAspects = AssessmentAspect::where('assessment_categories_id', $categoryId)->get();

        $inputNames = $request->input('name', []);
        $inputDescs = $request->input('description', []);

        $keptNames = [];

        foreach ($inputNames as $index => $name) {
            $desc = $inputDescs[$index] ?? '';

            $aspect = AssessmentAspect::updateOrCreate(
                ['assessment_categories_id' => $categoryId, 'name' => $name],
                ['desc' => $desc]
            );

            $keptNames[] = $aspect->name;
        }

        $namesToDelete = $existingAspects->pluck('name')->diff($keptNames);
        AssessmentAspect::where('assessment_categories_id', $categoryId)
            ->whereIn('name', $namesToDelete)
            ->delete();

        return redirect()->route('assesment-aspect.index')->with('success', 'Aspek Penilaian berhasil diperbarui.');
    }

}
