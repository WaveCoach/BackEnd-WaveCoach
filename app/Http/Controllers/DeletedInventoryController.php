<?php

namespace App\Http\Controllers;

use App\Models\inventory_management;
use Illuminate\Http\Request;

class DeletedInventoryController extends Controller
{
    public function index()
    {
        $inventory_management = inventory_management::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('pages.deleted_inventory.index', compact('inventory_management'));
    }
}
