<?php

namespace App\Http\Controllers;

use App\Models\RescheduleRequest;
use Illuminate\Http\Request;

class RescheduleRequestController extends Controller
{
    public function index(){
        $reschedules = RescheduleRequest::with('coach')->orderBy('id', 'desc')->get();
        // dd($reschedules);
        return view('pages.reschedule.index', compact('reschedules'));
    }

    public function edit($id){
        $reschedules = RescheduleRequest::with('coach')->orderBy('id', 'desc')->find($id);
        return view('pages.reschedule.edit', compact('reschedules'));
    }

    public function update(Request $request, $id)
    {
        $reschedules = RescheduleRequest::with('coach')->findOrFail($id);
        $reschedules->status = $request->status;
        $reschedules->response_message = $request->response_message;
        $reschedules->save();

        return redirect()->route('reschedule.index')
            ->with('success', 'permintaan reschedule berhasil diperbarui!');
    }
}
