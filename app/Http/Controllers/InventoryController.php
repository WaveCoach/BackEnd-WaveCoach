<?php

namespace App\Http\Controllers;

use App\Models\coaches;
use App\Models\inventory;
use App\Models\inventory_management;
use App\Models\User;
use Illuminate\Http\Request;

class InventoryController extends Controller
{

    public function index()
    {
        $inventorys = Inventory::orderBy('created_at', 'desc')->withSum('inventoryManagements', 'qty') ->get();
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
            coaches::create([
                'user_id' => $newUser->id
            ]);

            $mastercoachId = $newUser->id;
        }

        if (is_numeric($request->inventory_id)) {
            $inventoryId = $request->inventory_id;
        } else {
            $newInventory = Inventory::create([
                'name' => $request->inventory_id,
            ]);

            $inventoryId = $newInventory->id;
        }

        $inventoryManagement = inventory_management::where('inventory_id', $inventoryId)
        ->where('mastercoach_id', $mastercoachId)
        ->first();

        if ($inventoryManagement) {
            $inventoryManagement->increment('qty', $request->qty);
        } else {
            inventory_management::create([
                'inventory_id' => $inventoryId,
                'qty' => $request->qty,
                'mastercoach_id' => $mastercoachId,
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Inventory berhasil ditambahkan!');
    }



    public function show(string $id)
    {
        $inventory_id = inventory::findOrFail($id);
        $pemegang = inventory_management::with(['mastercoach', 'inventory'])->where('inventory_id', $id)->get();
        return view('pages.inventory.show', compact('pemegang'));
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
        ]);

        $inventory = inventory::findOrFail($id);
        $inventory->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Data berhasil diperbarui!');
    }


    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        inventory_management::where('inventory_id', $id)->delete();
        $inventory->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }

    public function inventDestroy($id)
    {
        $inventory_management = inventory_management::find($id);
        $inventory_management->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    public function inventedit($id){
        $pemegang = inventory_management::with(['mastercoach', 'inventory'])->find($id);
        $inventories = inventory::all();
        $mastercoaches = User::where('role_id', 3)->get();
        return view('pages.inventory.detailEdit', compact('pemegang', 'inventories', 'mastercoaches'));
    }

    public function inventUpdate(Request $request, $id)
    {
        $request->validate([
            'inventory_id' => 'required',
            'qty' => 'required|integer|min:1',
            'mastercoach_id' => 'required',
            'email' => 'nullable|email|max:255|unique:users,email,' . $id,
        ]);

        $inventoryManagement = inventory_management::findOrFail($id);

        if (is_numeric($request->mastercoach_id)) {
            $mastercoachId = $request->mastercoach_id;
        } else {
            $newUser = User::create([
                'name' => $request->mastercoach_id,
                'email' => $request->email,
                'password' => bcrypt('password123'),
                'role_id' => 3,
            ]);

            coaches::create([
                'user_id' => $newUser->id
            ]);

            $mastercoachId = $newUser->id;
        }

        if (is_numeric($request->inventory_id)) {
            $inventoryId = $request->inventory_id;
        } else {
            $newInventory = Inventory::create([
                'name' => $request->inventory_id,
            ]);

            $inventoryId = $newInventory->id;
        }

        $inventoryManagement->update([
            'inventory_id' => $inventoryId,
            'qty' => $request->qty,
            'mastercoach_id' => $mastercoachId,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Inventory berhasil diperbarui!');
    }


}
