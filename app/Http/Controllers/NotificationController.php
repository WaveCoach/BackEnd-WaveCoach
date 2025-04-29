<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $notifications = Notification::where('user_id', Auth::user()->id)->latest()->get();
        return response()->json($notifications);
    }

    public function getDetailNotif($id)
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return redirect()->back()->with('error', 'Notification not found');
        }

        $notification->update(['is_read' => 1]);

        if ($notification->type === 'schedule') {
            return redirect()->route('schedule.index');
        } elseif ($notification->type === 'reschedule') {
            return redirect()->route('reschedule.index');
        } elseif ($notification->type === 'absen') {
            return redirect()->route('attendance.coach');
        }

    }
}
