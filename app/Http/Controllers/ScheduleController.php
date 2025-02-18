<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScheduleController extends Controller
{

    public function index()
    {
        return view('pages.schedule.index');
    }


    public function create()
    {
        return view('pages.schedule.create');
    }


    public function store(Request $request)
    {
        //
    }


    public function show(string $id)
    {
        return view('pages.schedule.show');
    }


    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
