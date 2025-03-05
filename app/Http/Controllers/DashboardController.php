<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Location;
use App\Models\Schedule;
use App\Models\User;

class DashboardController extends Controller
{
    public function index() {
        $location = Location::count();
        $inventory = Inventory::count();
        $coaches = User::whereIn('role_id', [2, 3])->count();
        $mastercoach = User::where('role_id', 3)->count();
        $student = User::where('role_id', 4)->count();
        $schedule = Schedule::count();

        return view('pages.dashboard.index', compact('location', 'inventory', 'coaches', 'mastercoach', 'student', 'schedule'));
    }

}
