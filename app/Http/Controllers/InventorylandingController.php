<?php

namespace App\Http\Controllers;

use App\Models\InventoryLanding;

class InventorylandingController extends Controller
{
    public function index()
    {
        $inventorys = InventoryLanding::with(['coach', 'inventory', 'mastercoach'])->orderBy('created_at')->get();
        return view('pages.inventory_landing_history.index', compact('inventorys'));
    }
}
