<?php

namespace App\Http\Controllers;

use App\Models\coaches;
use App\Models\inventory;
use App\Models\InventoryLandings;
use App\Models\InventoryManagement;
use App\Models\InventoryReturns;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            'inventory_image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
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
            $existingInventory = Inventory::whereRaw('LOWER(name) = ?', [strtolower($request->inventory_id)])->first();

            if ($existingInventory) {
                $inventoryId = $existingInventory->id;
            } else {
                if ($request->hasFile('inventory_image')) {
                    $imagePath = $request->file('inventory_image')->store('inventory_images', 'public');
                } else {
                    $imagePath = null;
                }

                $newInventory = Inventory::create([
                    'name' => $request->inventory_id,
                    'inventory_image' => $imagePath,
                ]);

                $inventoryId = $newInventory->id;
            }
        }


        $inventoryManagement = InventoryManagement::where('inventory_id', $inventoryId)
        ->where('mastercoach_id', $mastercoachId)
        ->first();

        if ($inventoryManagement) {
            $inventoryManagement->increment('qty', $request->qty);
        } else {
            InventoryManagement::create([
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
        $pemegang = InventoryManagement::with(['mastercoach', 'inventory'])->where('inventory_id', $id)->get();
        return view('pages.inventory.show', compact('pemegang'));
    }


    public function edit(string $id)
    {
        $inventory = Inventory::findOrFail($id);
        return view('pages.inventory.edit', compact('inventory'));
    }


    public function update(Request $request, string $id)
    {
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'inventory_image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        // dd('Validasi berhasil', $validated);

        $inventory = Inventory::findOrFail($id);

        if ($request->hasFile('inventory_image')) {
            if ($inventory->inventory_image) {
                Storage::disk('public')->delete($inventory->inventory_image);
            }

            $imagePath = $request->file('inventory_image')->store('inventory_images', 'public');
        } else {
            $imagePath = $inventory->inventory_image;
        }

        $inventory->update([
            'name' => $request->name,
            'inventory_image' => $imagePath,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Data berhasil diperbarui!');
    }



    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);

        if (InventoryManagement::where('inventory_id', $id)->exists()) {
            return redirect()->back()->with('error', 'Data tidak bisa dihapus karena masih memiliki data peminjam!');
        }

        $inventory->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }


    public function inventDestroy($id)
    {
        $inventory_management = InventoryManagement::find($id);
        $inventory_management->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    public function inventedit($id){
        $pemegang = InventoryManagement::with(['mastercoach', 'inventory'])->find($id);
        $inventories = Inventory::all();
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

        $inventoryManagement = InventoryManagement::findOrFail($id);

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

    public function returnInventory(Request $request, $landingId)
    {
        $request->validate([
            'qty_returned' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $landing = InventoryLandings::findOrFail($landingId);

            if ($landing->status !== 'borrowed') {
                return response()->json(['message' => 'Barang belum dipinjam atau sudah dikembalikan!'], 400);
            }

            if ($request->qty_returned > $landing->qty_out) {
                return response()->json(['message' => 'Jumlah yang dikembalikan lebih dari yang dipinjam!'], 400);
            }

            $return = InventoryReturns::create([
                'inventory_landing_id' => $landing->id,
                'inventory_id'         => $landing->inventory_id,
                'mastercoach_id'       => $landing->mastercoach_id,
                'coach_id'             => $landing->coach_id,
                'qty_returned'         => $request->qty_returned,
                'returned_at'          => now(),
            ]);

            $inventory = InventoryManagement::where('inventory_id', $landing->inventory_id)
                ->where('mastercoach_id', $landing->mastercoach_id)
                ->first();

            if ($inventory) {
                $inventory->increment('qty', $request->qty_returned);
            }

            if ($request->qty_returned >= $landing->qty_out) {
                $landing->update(['status' => 'returned']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Barang berhasil dikembalikan!',
                'return'  => $return,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal mengembalikan barang!', 'error' => $e->getMessage()], 400);
        }
    }


}
