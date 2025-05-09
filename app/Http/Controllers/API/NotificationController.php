<?php

namespace App\Http\Controllers\API;

use App\Events\NotificationSent;
use App\Models\InventoryRequests;
use App\Models\InventoryReturns;
use App\Models\Notification;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;

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
            ->get();

        if ($notifications->isEmpty()) {
            return $this->ErrorResponse('Notifikasi tidak ditemukan', 404);
        }

        $formattedNotifications = $notifications->map(function ($notif) {
            return [
                'id' => $notif->id,
                'notifiable_type' => $notif->notifiable_type,
                'title' => $notif->title,
                'message' => $notif->message,
                'is_read' => $notif->is_read,
                'type' => $notif->type,
                'created_at' => Carbon::parse($notif->created_at)->translatedFormat('d F Y, H:i'),
                'user' => $notif->user ? [
                    'id' => $notif->user->id,
                    'name' => $notif->user->name,
                ] : null, // Jika user null, set null

                'pengirim' => $notif->pengirim ? [
                    'id' => $notif->pengirim->id,
                    'name' => $notif->pengirim->name,
                ] : null, // Jika pengirim null, set null
            ];
        });

        return $this->SuccessResponse($formattedNotifications, 'Daftar notifikasi');
    }


    public function getCountNotif()
    {

        $userId = Auth::id();

        if (!$userId) {
            return $this->ErrorResponse('Unauthorized', 401);
        }

        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', 0)
            ->count();

        return $this->SuccessResponse(['unread_count' => $unreadCount], 'Jumlah notifikasi belum dibaca');
    }

    public function getDetailNotif($NotifId)
    {
        $userId = Auth::id();

        if (!$userId) {
            return $this->ErrorResponse('Unauthorized', 401);
        }

        $notif = Notification::where('id', $NotifId)
            ->where('user_id', $userId)
            ->with(['user', 'pengirim'])
            ->first();

        if (!$notif) {
            return $this->ErrorResponse('Notifikasi tidak ditemukan', 404);
        }

        // Update is_read jadi 1
        if ($notif->is_read == 0) {
            $notif->update([
                'is_read' => 1,
            ]);
        }

        // Ambil detail model relasi berdasarkan notifiable_type
        $detail = null;
        $items = [];

        if ($notif->notifiable_type === InventoryRequests::class) {
            $detail = $notif->notifiable()->with(['items.inventory', 'mastercoach', 'coach'])->first();

            $items = $detail->items->map(function ($item) {
                return [
                    'id' => $item->inventory->id,
                    'item_name' => $item->inventory->name,   // GANTI jadi item_name
                    'quantity' => $item->qty_requested,
                ];
            });
        } elseif ($notif->notifiable_type === InventoryReturns::class) {
            $detail = $notif->notifiable()->with(['inventory', 'landing', 'mastercoach', 'coach'])->first();

            $items = [
                'id' => $detail->inventory->id ?? null,
                'item_name' => $detail->inventory->name ?? null,   // GANTI jadi item_name
                'quantity' => $detail->qty_returned ?? null,
            ];
        } elseif ($notif->notifiable_type === Schedule::class) {
            $detail = $notif->notifiable()->with(['coach', 'location'])->first();

            $items = [
                'id' => $detail->id ?? null,
                'schedule_summary' => $detail
                    ? \Carbon\Carbon::parse($detail->date)->translatedFormat('l, d F Y') . ', ' . substr($detail->start_time, 0, 5) . ' - ' . substr($detail->end_time, 0, 5)
                    : null,   // GANTI jadi schedule_summary
                'location' => $detail->location->name ?? null,
            ];
        } elseif ($notif->notifiable_type === RescheduleRequest::class) {
            $detail = $notif->notifiable()->with('coach')->first();

            $items = [
                'id' => $detail->id ?? null,
                'reschedule_reason' => $detail->reason ?? null,   // GANTI jadi reschedule_reason
            ];
        }


        return $this->SuccessResponse([
            'id' => $notif->id,
            'notifiable_id' => $notif->notifiable_id,
            'notifiable_type' => $notif->notifiable_type,
            'title' => $notif->title,
            'message' => $notif->message,
            'created_at' => Carbon::parse($notif->created_at)->translatedFormat('d F Y, H:i'),
            'is_read' => $notif->is_read,
            'type' => $notif->type,
            'user' => [
                'id' => $notif->user->id,
                'name' => $notif->user->name,
            ],
            'pengirim' => [
                'id' => $notif->pengirim->id,
                'name' => $notif->pengirim->name,
            ],
            'items' => $items
        ], 'Detail notifikasi');
    }

    public function sendNotification(Request $request)
    {
        $pusher = new Pusher('2d59d7b8156ef1107a27', '1a48c7a8f6fa9594ddcc', '1982308', [
            'cluster' => 'ap1',
            'useTLS' => true
        ]);


        $pusher->trigger('notification-channel', 'NotificationSent', [
            'message' => "Hello cintaaaaaa, this is a test notification!",
        ]);

        return response()->json(['success' => true]);
    }


}
