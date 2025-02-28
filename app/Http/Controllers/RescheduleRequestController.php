<?php

namespace App\Http\Controllers;

use App\Models\RescheduleRequest;
use Illuminate\Http\Request;

class RescheduleRequestController extends Controller
{
    public function index(){
        $reschedule = RescheduleRequest::orderBy('id', 'desc')->get();
        return view('pages.reschedule.index', compact('reschedule'));
    }
}
