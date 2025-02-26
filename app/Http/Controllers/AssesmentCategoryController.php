<?php

namespace App\Http\Controllers;

use App\Models\assesment_aspect;
use App\Models\assesment_category;
use Illuminate\Http\Request;

class AssesmentCategoryController extends Controller
{

    public function index()
    {
        $categories = assesment_category::all();
        return view('pages.assesment_category.index', compact('categories'));
    }


    public function create()
    {
        return view('pages.assesment_category.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        assesment_category::create([
            'name' => $request->name,
        ]);

        return redirect()->route('assesment-category.index')->with('success', 'Assessment category created successfully');
    }


    public function show(string $id)
    {
        $category = assesment_category::findOrFail($id);
        return view('pages.assesment_category.show', compact('category'));
    }


    public function edit(string $id)
    {
        $category = assesment_category::findOrFail($id);
        return view('pages.assesment_category.edit', compact('category'));
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = assesment_category::findOrFail($id);
        $category->update([
            'name' => $request->name,
        ]);

        return redirect()->route('assesment-category.index')->with('success', 'Assessment category updated successfully');
    }


    public function destroy(string $id)
    {
        $category = assesment_category::findOrFail($id);
        $category->delete();
        assesment_aspect::where('assesment_categories_id', $id)->delete();

        return redirect()->route('assesment-aspect.index')->with('success', 'Assessment category deleted successfully');
    }
}
