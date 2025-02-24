<?php

namespace App\Http\Controllers;

use App\Models\inventory;
use App\Models\inventory_landing;
use App\Models\inventory_management;
use App\Models\User;
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
        $inventorys = Inventory::orderBy('created_at', 'desc')->get();
        $mastercoaches = User::where('role_id', 3)->get();
        return view('pages.inventory.create', compact('inventorys', 'mastercoaches'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required',
            'qty' => 'required|integer|min:1',
            'mastercoach_id' => 'required',
            'email' => 'nullable|email|max:255|unique:users,email',
        ]);

        if (is_numeric($request->mastercoach_id)) {
            $mastercoachId = $request->mastercoach_id;
        } else {
            $newUser = User::create([
                'name' => $request->mastercoach_id,
                'email' => $request->email,
                'password' => bcrypt('password123'),
                'role_id' => 3,
            ]);

            $mastercoachId = $newUser->id;
        }

        if (is_numeric($request->inventory_id)) {
            $inventoryId = $request->inventory_id;
        } else {
            $newInventory = Inventory::create([
                'name' => $request->inventory_id, // Sesuaikan dengan field di tabel Inventory
            ]);

            $inventoryId = $newInventory->id;
        }

        inventory_management::create([
            'inventory_id' => $inventoryId,
            'qty' => $request->qty,
            'mastercoach_id' => $mastercoachId,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Inventory berhasil ditambahkan!');
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
