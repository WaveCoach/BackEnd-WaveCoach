<?php

namespace App\Http\Controllers;

use App\Models\assesment_aspect;
use App\Models\inventory;
use App\Models\location;
use App\Models\schedule;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        $location = location::count();
        $inventory = inventory::count();
        $coaches = User::whereIn('role_id', [2, 3])->count();
        $mastercoach = User::where('role_id', 3)->count();
        $student = User::where('role_id', 4)->count();
        $schedule = schedule::count();

        return view('pages.dashboard.index', compact('location', 'inventory', 'coaches', 'mastercoach', 'student', 'schedule'));
    }

}
