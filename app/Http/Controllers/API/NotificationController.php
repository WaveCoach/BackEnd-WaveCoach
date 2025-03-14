<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends BaseController
{
    public function getNotif()
    {
        $userId = Auth::id();

        if (!$userId) {
            return $this->ErrorResponse('Unauthorized', 401);
        }

        $notifications = Notification::with(['user', 'pengirim'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif -> id,
                    'notifiable_type' => $notif->notifiable_type,
                    'title' => $notif->title,
                    'message' => $notif->message,
                    'is_read' => $notif->is_read,
                    'type' => $notif->type,
                    'created_at' => Carbon::parse($notif->created_at)->translatedFormat('d F Y, H:i'),
                    'user' => [
                        'id' => $notif->user->id,
                        'name' => $notif->user->name,
                    ],
                    'pengirim' => [
                        'id' => $notif->pengirim->id,
                        'name' => $notif->pengirim->name,
                    ],
                ];
            });

        return $this->SuccessResponse($notifications, 'Daftar notifikasi');
    }

}
