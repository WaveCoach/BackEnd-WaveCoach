<?php

namespace App\Http\Controllers;

use App\Models\InventoryLanding;
use App\Models\InventoryLandings;

class InventorylandingController extends Controller
{
    public function index()
    {
        $inventorys = InventoryLandings::with(['coach', 'inventory', 'mastercoach'])->orderBy('created_at')->get();
        return view('pages.inventory_landing_history.index', compact('inventorys'));
    }
}
