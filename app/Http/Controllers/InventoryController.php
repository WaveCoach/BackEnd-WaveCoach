<?php

namespace App\Http\Controllers;

use App\Models\inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{

    public function index()
    {
        $inventorys = Inventory::orderBy('created_at', 'desc')->get();
        return view('pages.inventory.index', compact('inventorys'));
    }

    public function create()
    {
        return view('pages.inventory.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total_quantity' => 'required',
        ]);

        inventory::create([
            'name' => $request->name,
            'total_quantity' => $request->total_quantity,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Data berhasil disimpan!');
    }


    public function show(string $id)
    {
        $inventory = inventory::findOrFail($id);
        return view('pages.inventory.show', compact('inventory'));
    }


    public function edit(string $id)
    {
        $inventory = inventory::findOrFail($id);
        return view('pages.inventory.edit', compact('inventory'));
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total_quantity' => 'required',
        ]);

        $inventory = inventory::findOrFail($id);
        $inventory->update([
            'name' => $request->name,
            'total_quantity' => $request->total_quantity,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Data berhasil diperbarui!');
    }


    public function destroy(string $id)
    {
        $inventory = inventory::findOrFail($id);
        $inventory->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }

}
