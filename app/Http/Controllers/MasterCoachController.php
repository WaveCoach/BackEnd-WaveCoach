<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MasterCoachController extends Controller
{

    public function index()
    {
        return view('pages.mastercoach.index');
    }


    public function create()
    {
        return view('pages.mastercoach.create');
    }


    public function store(Request $request)
    {
        //
    }


    public function show(string $id)
    {
        return view('pages.mastercoach.show');
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
