<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventorylandingController extends Controller
{
    public function index()
    {
        return view('pages.inventory_landing_history.index');
    }
}
