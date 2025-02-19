<?php

namespace App\Http\Controllers;

use App\Models\assesment_aspect;
use App\Models\assesment_category;
use Illuminate\Http\Request;

class AssesmentAspectController extends Controller
{

    public function index()
    {
        $categories = assesment_category::with('aspects')->get();
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
            'name' => 'required',
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
        $aspect = assesment_aspect::findOrFail($id);
        return view('pages.assesment_aspect.show', compact('aspect'));
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
        $aspects = assesment_aspect::find($id);

        if (!$aspects) {
            return redirect()->route('assesment-aspect.index')->with('error', 'aspects not found');
        }

        $aspects->delete();
        return redirect()->route('assesment-aspect.index')->with('success', 'aspects deleted successfully');
    }
}
