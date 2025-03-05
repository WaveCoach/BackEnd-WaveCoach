<?php

namespace App\Http\Controllers;

use App\Models\inventory_management;
use App\Models\InventoryManagement;
use Illuminate\Http\Request;

class DeletedInventoryController extends Controller
{
    public function index()
    {
        $inventory_management = InventoryManagement::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('pages.deleted_inventory.index', compact('inventory_management'));
    }
}
