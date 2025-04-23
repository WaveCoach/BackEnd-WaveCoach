<?php

namespace App\Http\Controllers;

use App\Models\AssessmentAspect;
use App\Models\AssessmentCategory;
use App\Models\Package;
use App\Models\PackageCategory;
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
        $packages = Package::orderBy('created_at', 'desc')->get();
        return view('pages.assesment_aspect.create', compact('categories', 'packages'));
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
            'package_id' => 'required|array|min:1',
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
        $packageIds = $request->input('package_id');

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

        foreach ($packageIds as $packageId) {
            PackageCategory::create([
                'category_id' => $categoryId,
                'package_id' => $packageId,
            ]);
        }

        return redirect()->route('assesment-aspect.index')->with('success', 'Aspek Penilaian dan Package berhasil ditambahkan');
    }


    public function show(string $id)
    {
        $categories = AssessmentCategory::with(['aspects', 'packages'])->findOrFail($id);
        return view('pages.assesment_aspect.show', compact('categories'));
    }

    // public function edit(string $id)
    // {
    //     $aspect = AssessmentAspect::findOrFail($id);
    //     return view('pages.assesment_aspect.edit', compact('aspect'));
    // }


    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //     ]);

    //     $aspect = AssessmentAspect::findOrFail($id);

    //     $existingAspect = AssessmentAspect::where('assessment_categories_id', $aspect->assessment_categories_id)
    //         ->where('name', $request->name)
    //         ->where('id', '!=', $id)
    //         ->exists();

    //     if ($existingAspect) {
    //         return back()->withErrors(['name' => 'Aspek dengan nama ini sudah ada dalam kategori yang sama.']);
    //     }

    //     $aspect->update([
    //         'name' => $request->name,
    //     ]);

    //     return redirect()->route('assesment-aspect.index')->with('success', 'Aspek berhasil diperbarui.');
    // }



    // public function destroy(string $id)
    // {
    //     $aspect = AssessmentAspect::find($id);

    //     if (!$aspect) {
    //         return redirect()->route('assesment-aspect.index')->with('error', 'Aspek tidak ditemukan');
    //     }

    //     $aspect->delete();
    //     return redirect()->route('assesment-aspect.index')->with('success', 'Aspek berhasil dihapus');
    // }

    public function asessmentedit($id){
        $category = AssessmentCategory::with('packages')->findOrFail($id);
        $aspects = AssessmentAspect::where('assessment_categories_id', $id)->get();
        $packages = Package::all();

        $selectedPackages = $category->packages->pluck('id')->toArray();
        return view('pages.assesment_aspect.aspectedit', compact('category', 'aspects', 'packages', 'selectedPackages'));
    }

    public function asessmentupdate(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'assessment_categories_id' => 'required',
            'category_name' => 'required|string|max:255',
            'kkm' => 'nullable|numeric|min:0|max:100',
            'name' => 'required|array|min:1',
            'name.*' => 'required|string|max:255',
            'description' => 'required|array|min:1',
            'description.*' => 'required|string|max:1000',
            'package_id' => 'required|array|min:1',
            'package_id.*' => 'integer|exists:packages,id',
        ]);

        AssessmentCategory::where('id', $id)->update([
            'name' => $request->category_name,
            'kkm' => $request->kkm,
        ]);

        $categoryId = $id;
        // Simpan aspek penilaian
        $inputNames = $request->input('name', []);
        $inputDescs = $request->input('description', []);
        $keptIds = [];

        foreach ($inputNames as $index => $name) {
            $desc = $inputDescs[$index] ?? '';

            $aspect = AssessmentAspect::updateOrCreate(
                [
                    'assessment_categories_id' => $categoryId,
                    'name' => $name,
                ],
                [
                    'desc' => $desc,
                ]
            );

            $keptIds[] = $aspect->id;
        }

        // Hapus aspek yang tidak ada dalam update
        AssessmentAspect::where('assessment_categories_id', $categoryId)
            ->whereNotIn('id', $keptIds)
            ->delete();

        // Update relasi Package
        PackageCategory::where('category_id', $categoryId)->delete(); // clear old
        foreach ($request->package_id as $packageId) {
            PackageCategory::create([
                'category_id' => $categoryId,
                'package_id' => $packageId,
            ]);
        }

        return redirect()->route('assesment-aspect.index')
            ->with('success', 'Aspek Penilaian dan Package berhasil diperbarui.');
    }


}
