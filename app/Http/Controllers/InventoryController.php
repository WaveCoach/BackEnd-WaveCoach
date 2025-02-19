<?php

namespace App\Http\Controllers;

use App\Models\inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{

    public function index()
    {
        $inventory = Inventory::orderBy('created_at', 'desc')->get();
        return view('pages.inventory.index', compact('inventory'));
    }

}
