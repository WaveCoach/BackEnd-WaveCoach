<?php

namespace App\Http\Controllers;

use App\Models\inventory_landing;
use Illuminate\Http\Request;

class InventorylandingController extends Controller
{
    public function index()
    {
        $inventorys = inventory_landing::with(['coach', 'inventory', 'mastercoach'])->orderBy('created_at')->get();
        return view('pages.inventory_landing_history.index', compact('inventorys'));
    }
}
