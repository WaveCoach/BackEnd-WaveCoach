<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\InventoryManagement;
use Illuminate\Http\Request;

class InventoryManagementController extends Controller
{
    public function index()
    {
        // Mengambil data semua inventory management bersama dengan relasi mastercoach dan inventory
        $inventoryManagement = InventoryManagement::with(['mastercoach', 'inventory'])
            ->get()
            ->groupBy('mastercoach_id'); // Mengelompokkan berdasarkan mastercoach_id

        // Mengembalikan response dalam format JSON
        return response()->json($inventoryManagement);
    }
}
