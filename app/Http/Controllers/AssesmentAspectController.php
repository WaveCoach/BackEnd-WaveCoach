<?php

namespace App\Http\Controllers;

use App\Models\assesment_aspect;
use Illuminate\Http\Request;

class AssesmentAspectController extends Controller
{

    public function index()
    {
        $aspects = assesment_aspect::all();
        return view('pages.assesment_aspect.index', compact('aspects'));
    }


    public function create()
    {
        return view('pages.assesment_aspect.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'assesment_categories_id' => 'required|exists:assessment_categories,id',
            'name' => 'required|string|max:255',
        ]);

        assesment_aspect::create([
            'assesment_categories_id' => $request->assesment_categories_id,
            'name' => $request->name,
        ]);

        return redirect()->route('assesment-aspect.index')->with('success', 'admin berhasil ditambahkan dengan password: ');
    }


    public function show(string $id)
    {
        $aspect = assesment_aspect::findOrFail($id);
        return view('pages.assesment_aspect.show', compact('aspect'));
    }


    public function edit(string $id)
    {
        $aspect = assesment_aspect::findOrFail($id);
        return view('pages.assesment_aspect.show', compact('aspect'));
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'assesment_categories_id' => 'required|exists:assessment_categories,id',
            'name' => 'required|string|max:255',
        ]);

        $aspect = assesment_aspect::findOrFail($id);
        $aspect->update([
            'assesment_categories_id' => $request->assesment_categories_id,
            'name' => $request->name,
        ]);

        return redirect()->route('assesment-aspect')->with('success', 'Assessment aspect updated successfully');
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
