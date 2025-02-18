<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CoachController extends Controller
{

    public function index()
    {
        return view('pages.coach.index');
    }


    public function create()
    {
        return view('pages.coach.create');
    }


    public function store(Request $request)
    {
        //
    }


    public function show(string $id)
    {
        return view('pages.coach.show');
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
