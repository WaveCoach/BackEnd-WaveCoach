<?php

namespace App\Http\Controllers\API;

use App\Models\Inventory;
use App\Models\InventoryLandings;
use App\Models\InventoryManagement;
use App\Models\InventoryRequestItem;
use App\Models\InventoryRequests;
use App\Models\InventoryReturns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Pusher\Pusher;

class InventoryController extends BaseController
{
    public function requestLoan(Request $request)
    {
        $request->validate([
            'mastercoach_id'   => 'required|exists:users,id',
            'tanggal_pinjam'   => 'required|date',
            'tanggal_kembali'  => 'required|date|after:tanggal_pinjam',
            'alasan_pinjam'    => 'required|string',
            'items'            => 'required|array',
            'items.*.inventory_id' => 'required|exists:inventories,id',
            'items.*.qty_requested' => 'required|integer|min:1',
        ]);

        $existingRequest = InventoryRequests::where('mastercoach_id', $request->mastercoach_id)
            ->where('coach_id', Auth::id())
            ->whereDate('tanggal_pinjam', $request->tanggal_pinjam)
            ->where('status', 'pending')
            ->whereHas('items', function ($query) use ($request) {
                $query->whereIn('inventory_id', array_column($request->items, 'inventory_id'));
            })
            ->exists();

        if ($existingRequest) {
            return $this->ErrorResponse(
                'Anda sudah mengajukan permintaan untuk barang yang sama pada tanggal ini. Silakan ajukan barang yang berbeda!',
                400
            );
        }

        DB::beginTransaction();

        try {
            $loanRequest = InventoryRequests::create([
                'mastercoach_id'  => $request->mastercoach_id,
                'coach_id'        => Auth::id(),
                'tanggal_pinjam'  => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'alasan_pinjam'   => $request->alasan_pinjam,
                'status'          => 'pending',
            ]);

            foreach ($request->items as $item) {
                $inventory = InventoryManagement::where('mastercoach_id', $request->mastercoach_id)
                    ->where('inventory_id', $item['inventory_id'])
                    ->first();

                if (!$inventory) {
                    throw new \Exception("Mastercoach ini tidak memiliki barang dengan ID {$item['inventory_id']}");
                }

                if ($inventory->qty < $item['qty_requested']) {
                    throw new \Exception("Stok tidak cukup untuk barang dengan ID {$item['inventory_id']}");
                }

                InventoryRequestItem::create([
                    'request_id'   => $loanRequest->id,
                    'inventory_id' => $item['inventory_id'],
                    'qty_requested' => $item['qty_requested'],
                ]);
            }

            \App\Models\Notification::create([
                'user_id'         => $loanRequest->mastercoach_id,
                'pengirim_id'     => $loanRequest->coach_id,
                'notifiable_id'   => $loanRequest->id,
                'notifiable_type' => \App\Models\InventoryRequests::class,
                'title'           => 'Permintaan Peminjaman Barang',
                'message'         => "Peminjaman oleh {$loanRequest->coach->name} telah diajukan untuk barang tertentu.",
                'type'            => 'request',
                'is_read'         => 0,
            ]);

            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                [
                    'cluster' => env('PUSHER_APP_CLUSTER'),
                    'useTLS' => true,
                ]
            );

            // Kirim event ke Pusher
            $pusher->trigger('notification-channel-user-' . $loanRequest->mastercoach_id, 'NotificationSent', [
                'message' => "Peminjaman oleh {$loanRequest->coach->name} telah diajukan untuk barang tertentu.",
                'title'   => 'Permintaan Peminjaman Barang',
                'type'    => 'request',
            ]);

            DB::commit();

            return $this->SuccessResponse(
                $loanRequest->load('items'),
                'Permintaan peminjaman berhasil diajukan!',
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->ErrorResponse('Gagal mengajukan peminjaman!', 400, ['error' => $e->getMessage()]);
        }
    }

    public function updateLoanStatus(Request $request, $requestId)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,borrowed',
            'rejection_reason' => 'nullable|string|required_if:status,rejected',
        ]);

        DB::beginTransaction();

        try {
            $loanRequest = InventoryRequests::with('items')->findOrFail($requestId);

            if (!in_array($loanRequest->status, ['pending', 'approved'])) {
                return $this->ErrorResponse('Permintaan peminjaman sudah diproses sebelumnya!', 400);
            }

            if ($request->status === 'approved') {
                foreach ($loanRequest->items as $item) {
                    $inventory = InventoryManagement::where('mastercoach_id', $loanRequest->mastercoach_id)
                        ->where('inventory_id', $item->inventory_id)
                        ->first();

                    if (!$inventory || $inventory->qty < $item->qty_requested) {
                        throw new \Exception("Stok tidak cukup untuk barang ID {$item->inventory_id}");
                    }

                    $inventory->decrement('qty', $item->qty_requested);

                    // Simpan ke InventoryLanding
                    InventoryLandings::create([
                        'request_id'   => $loanRequest->id,
                        'inventory_id' => $item->inventory_id,
                        'coach_id'     => $loanRequest->coach_id,
                        'mastercoach_id' => $loanRequest->mastercoach_id,
                        'tanggal_pinjam' => $loanRequest->tanggal_pinjam,
                        'tanggal_kembali' => $loanRequest->tanggal_kembali,
                        'qty_borrowed' => $item->qty_requested,
                        'qty_returned' => 0,
                        'qty_pending_return' => 0,
                        'status'  => 'borrowed',
                    ]);
                }

                $loanRequest->update([
                    'status' => 'approved',
                    'rejection_reason' => $request->rejection_reason,
                ]);

                $loanRequest->notifications()->create([
                    'pengirim_id'     => $loanRequest->mastercoach_id,
                    'user_id'         => $loanRequest->coach_id,
                    'notifiable_id'   => $loanRequest->id,
                    'notifiable_type' => InventoryRequests::class,
                    'title'           => 'Peminjaman Disetujui',
                    'message'         => "Permintaan peminjaman Anda untuk tanggal {$loanRequest->tanggal_pinjam} telah disetujui oleh Mastercoach.",
                    'type'            => 'request',
                    'is_read'         => 0,
                ]);

                $pusher = new Pusher(
                    env('PUSHER_APP_KEY'),
                    env('PUSHER_APP_SECRET'),
                    env('PUSHER_APP_ID'),
                    [
                        'cluster' => env('PUSHER_APP_CLUSTER'),
                        'useTLS' => true,
                    ]
                );

                // Kirim event ke Pusher
                $pusher->trigger('notification-channel-user-' . $loanRequest->coach_id, 'NotificationSent', [
                    'message' => "Permintaan peminjaman Anda untuk tanggal {$loanRequest->tanggal_pinjam} telah disetujui oleh Mastercoach.",
                    'title'   => 'Peminjaman Disetujui',
                    'type'    => 'request',
                ]);


            } elseif ($request->status === 'rejected') {
                $loanRequest->update([
                    'status' => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                ]);

                $loanRequest->notifications()->create([
                    'pengirim_id'     => $loanRequest->mastercoach_id,
                    'user_id'         => $loanRequest->coach_id, // Coach yang mengajukan peminjaman
                    'notifiable_id'   => $loanRequest->id,
                    'notifiable_type' => \App\Models\InventoryRequests::class,
                    'title'           => 'Peminjaman Ditolak',
                    'message'         => "Permintaan peminjaman Anda ditolak. Alasan: {$request->rejection_reason}",
                    'type'            => 'request',
                    'is_read'         => 0,
                ]);

                $pusher = new Pusher(
                    env('PUSHER_APP_KEY'),
                    env('PUSHER_APP_SECRET'),
                    env('PUSHER_APP_ID'),
                    [
                        'cluster' => env('PUSHER_APP_CLUSTER'),
                        'useTLS' => true,
                    ]
                );

                // Kirim event ke Pusher
                $pusher->trigger('notification-channel-user-' . $loanRequest->coach_id, 'NotificationSent', [
                    'message' => "Permintaan peminjaman Anda ditolak. Alasan: {$request->rejection_reason}",
                    'title'   => 'Peminjaman Ditolak',
                    'type'    => 'request',
                ]);

            }

            DB::commit();

            return $this->SuccessResponse($loanRequest, "Permintaan peminjaman telah {$request->status}!", 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->ErrorResponse('Gagal memperbarui status peminjaman!', 400, ['error' => $e->getMessage()]);
        }
    }


    public function returnInventory(Request $request, $landingId)
    {

        // dd($request->qty_returned);
        $request->validate([
            'qty_returned'   => 'required|integer|min:1',
            'img'             => 'nullable|string', // Validasi Base64 (string)
            'damaged_count'   => 'nullable|integer|min:0',
            'missing_count'   => 'nullable|integer|min:0',
            'returned_at'     => 'nullable|date',
            'desc'           => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $landing = InventoryLandings::findOrFail($landingId);

            if ($landing->status !== 'borrowed') {
                return $this->ErrorResponse('Barang belum dipinjam atau sudah dikembalikan!', 400);
            }

            if ($request->qty_returned > $landing->qty_borrowed) {
                return $this->ErrorResponse('Jumlah yang dikembalikan lebih dari yang dipinjam!', 400);
            }

            $existingReturn = InventoryReturns::where('inventory_landing_id', $landing->id)
                ->where('status', 'pending')
                ->exists();

            if ($existingReturn) {
                return $this->ErrorResponse('Pengajuan pengembalian untuk barang ini sudah dibuat dan masih menunggu persetujuan!', 400);
            }

            // Proses gambar jika ada (menangani Base64 atau file upload)
            $imagePath = null;
            if ($request->has('img') && !empty($request->img)) {
                $imagePath = $this->uploadBase64Image($request->img, 'public/return_inventaris');
            }

            // Buat pengajuan pengembalian
            $return = InventoryReturns::create([
                'inventory_landing_id' => $landing->id,
                'inventory_id'         => $landing->inventory_id,
                'mastercoach_id'       => $landing->mastercoach_id,
                'coach_id'             => $landing->coach_id,
                'qty_returned'         => $request->qty_returned,
                'returned_at'          => $request->returned_at ?? now(),
                'img_inventory_return' => $imagePath,
                'damaged_count'        => $request->damaged_count ?? 0,
                'missing_count'        => $request->missing_count ?? 0,
                'desc'                 => $request->desc,
                'status'               => 'pending', // Status pengembalian
            ]);

            // Buat notifikasi untuk mastercoach
            $coachName     = $landing->coach->name ?? 'Coach';
            $inventoryName = $landing->inventory->name ?? 'Barang';
            $return->notifications()->create([
                'pengirim_id'     => $landing->coach_id,
                'user_id'         => $landing->mastercoach_id,
                'notifiable_id'   => $return->id,
                'notifiable_type' => \App\Models\InventoryReturns::class,
                'title'           => 'Pengajuan Pengembalian Barang',
                'message'         => "{$coachName} telah mengajukan pengembalian barang *{$inventoryName}*.",
                'type'            => 'return',
                'is_read'         => 0,
            ]);

            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                [
                    'cluster' => env('PUSHER_APP_CLUSTER'),
                    'useTLS' => true,
                ]
            );

            // Kirim event ke Pusher
            $pusher->trigger('notification-channel-user-' . $landing->mastercoach_id, 'NotificationSent', [
                'message' => "{$coachName} telah mengajukan pengembalian barang *{$inventoryName}*.",
                'title'   => 'Pengajuan Pengembalian Barang',
                'type'    => 'return',
            ]);

            DB::commit();

            // Kembalikan respon sukses
            return $this->SuccessResponse($return, 'Pengajuan pengembalian berhasil dibuat, menunggu persetujuan mastercoach.', 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->ErrorResponse('Gagal mengajukan pengembalian!', 400, ['error' => $e->getMessage()]);
        }
    }

    private function uploadBase64Image(string $base64Image, string $folder = 'images'): string
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            throw new \Exception('Format gambar tidak valid.');
        }

        $imageType = strtolower($type[1]);
        $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
        $base64Image = base64_decode($base64Image);

        if ($base64Image === false) {
            throw new \Exception('Base64 decode gagal.');
        }

        $filename = uniqid('return_', true) . '.' . $imageType;
        $filePath = "{$folder}/{$filename}";

        Storage::disk('public')->put($filePath, $base64Image);

        return $filePath;
    }

    public function updateReturnStatus(Request $request, $returnId)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|string|required_if:status,rejected',
        ]);

        DB::beginTransaction();

        try {
            $returnRequest = InventoryReturns::findOrFail($returnId);

            if ($returnRequest->status !== 'pending') {
                return $this->ErrorResponse('Pengembalian sudah diproses sebelumnya!', 400);
            }

            // Ambil data coach dan inventory
            $coachName     = $returnRequest->coach->name ?? 'Coach';
            $inventoryName = $returnRequest->inventory->name ?? 'Barang';

            if ($request->status === 'approved') {
                $landing = InventoryLandings::findOrFail($returnRequest->inventory_landing_id);

                if ($landing->qty_borrowed < $returnRequest->qty_returned) {
                    throw new \Exception("Jumlah qty_borrowed tidak mencukupi untuk dikembalikan.");
                }

                $landing->decrement('qty_returned', $returnRequest->qty_returned);
                $landing->decrement('qty_pending_return', $landing->qty_borrowed - $returnRequest->qty_returned);

                if ($landing->qty_borrowed == 0) {
                    $landing->update(['status' => 'returned']);
                }

                $inventory = InventoryManagement::where('mastercoach_id', $returnRequest->mastercoach_id)
                    ->where('inventory_id', $returnRequest->inventory_id)
                    ->first();
                $inventory->increment('qty', $returnRequest->qty_returned);

                $returnRequest->updateOrFail(['status' => 'approved']);

                // Buat notifikasi
                $returnRequest->notifications()->create([
                    'pengirim_id' => $returnRequest->mastercoach_id,
                    'user_id'     => $returnRequest->coach_id,
                    'title'       => 'Pengembalian Barang Disetujui',
                    'message'     => "Pengembalian barang *{$inventoryName}* oleh {$coachName} telah disetujui oleh Mastercoach.",
                    'type'        => 'return',
                    'is_read'     => 0,
                ]);

                $pusher = new Pusher(
                    env('PUSHER_APP_KEY'),
                    env('PUSHER_APP_SECRET'),
                    env('PUSHER_APP_ID'),
                    [
                        'cluster' => env('PUSHER_APP_CLUSTER'),
                        'useTLS' => true,
                    ]
                );

                // Kirim event ke Pusher
                $pusher->trigger('notification-channel-user-' . $returnRequest->coach_id, 'NotificationSent', [
                    'message' => "Pengembalian barang *{$inventoryName}* oleh {$coachName} telah disetujui oleh Mastercoach.",
                    'title'   => 'Pengembalian Barang Disetujui',
                    'type'    => 'return',
                ]);

            } elseif ($request->status === 'rejected') {
                $returnRequest->updateOrFail([
                    'status' => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                ]);

                // Buat notifikasi
                $returnRequest->notifications()->create([
                    'pengirim_id' => $returnRequest->mastercoach_id,
                    'user_id'     => $returnRequest->coach_id,
                    'title'       => 'Pengembalian Barang Ditolak',
                    'message'     => "Pengembalian barang *{$inventoryName}* oleh {$coachName} ditolak. Alasan: {$request->rejection_reason}.",
                    'type'        => 'return',
                    'is_read'     => 0,
                ]);

                $pusher = new Pusher(
                    env('PUSHER_APP_KEY'),
                    env('PUSHER_APP_SECRET'),
                    env('PUSHER_APP_ID'),
                    [
                        'cluster' => env('PUSHER_APP_CLUSTER'),
                        'useTLS' => true,
                    ]
                );

                // Kirim event ke Pusher
                $pusher->trigger('notification-channel-user-' . $returnRequest->coach_id, 'NotificationSent', [
                    'message' => "Pengembalian barang *{$inventoryName}* oleh {$coachName} ditolak. Alasan: {$request->rejection_reason}.",
                    'title'   => 'Pengembalian Barang Ditolak',
                    'type'    => 'return',
                ]);
            }

            DB::commit();
            return $this->SuccessResponse($returnRequest, "Pengembalian telah {$request->status}!", 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->ErrorResponse('Gagal memperbarui status pengembalian!', 400, ['error' => $e->getMessage()]);
        }
    }

    public function getHistory(Request $request)
    {
        $userId = Auth::id();
        $roleId = Auth::user()->role_id;
        $filter = $request->input('filter');

        $requestsQuery = DB::table('inventory_requests')
            ->select(
                'inventory_requests.id',
                'inventory_requests.mastercoach_id',
                'inventory_requests.coach_id',
                'inventory_requests.status',
                'inventory_requests.created_at',
                'inventory_requests.updated_at',
                DB::raw("'request' AS type"),
                DB::raw(($roleId == 2 ? 'master.name' : 'coach.name') . ' AS coach_name'),
                DB::raw("CONCAT('" . url('storage') . "/', " . ($roleId == 2 ? 'master.profile_image' : 'coach.profile_image') . ") AS profile_image")
            )
            ->leftJoin('users as coach', 'coach.id', '=', 'inventory_requests.coach_id')
            ->leftJoin('users as master', 'master.id', '=', 'inventory_requests.mastercoach_id');

        $returnsQuery = DB::table('inventory_returns')
            ->select(
                'inventory_returns.id',
                'inventory_returns.mastercoach_id',
                'inventory_returns.coach_id',
                'inventory_returns.status',
                'inventory_returns.created_at',
                'inventory_returns.updated_at',
                DB::raw("'return' AS type"),
                DB::raw(($roleId == 3 ? 'coach.name' : 'master.name') . ' AS coach_name'),
                DB::raw("CONCAT('" . url('storage') . "/', " . ($roleId == 3 ? 'coach.profile_image' : 'master.profile_image') . ") AS profile_image")
            )
            ->leftJoin('users as coach', 'coach.id', '=', 'inventory_returns.coach_id')
            ->leftJoin('users as master', 'master.id', '=', 'inventory_returns.mastercoach_id');

        // Apply filters
        if ($filter === 'masuk') {
            $requestsQuery->where('inventory_requests.mastercoach_id', $userId);
            $returnsQuery->where('inventory_returns.mastercoach_id', $userId);
        } elseif ($filter === 'keluar') {
            $requestsQuery->where('inventory_requests.coach_id', $userId);
            $returnsQuery->where('inventory_returns.coach_id', $userId);
        } else {
            $requestsQuery->where(function ($query) use ($userId) {
                $query->where('inventory_requests.mastercoach_id', $userId)
                    ->orWhere('inventory_requests.coach_id', $userId);
            });

            $returnsQuery->where(function ($query) use ($userId) {
                $query->where('inventory_returns.mastercoach_id', $userId)
                    ->orWhere('inventory_returns.coach_id', $userId);
            });
        }

        // Gabungkan dengan unionAll lalu bungkus dalam subquery untuk bisa diurutkan
        $union = $requestsQuery->unionAll($returnsQuery);

        $inventory = DB::table(DB::raw("({$union->toSql()}) as combined"))
            ->mergeBindings($union)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) use ($userId) {
                $item->created_at = \Carbon\Carbon::parse($item->created_at)->timezone('Asia/Jakarta')->toDateTimeString();
                $item->updated_at = \Carbon\Carbon::parse($item->updated_at)->timezone('Asia/Jakarta')->toDateTimeString();

                // Tambahkan condition berdasarkan userId
                if ($item->coach_id == $userId) {
                    $item->condition = 'keluar';
                } elseif ($item->mastercoach_id == $userId) {
                    $item->condition = 'masuk';
                } else {
                    $item->condition = 'unknown';
                }

                return $item;
            });

        return $this->SuccessResponse($inventory, 'Data history berhasil diambil.');
    }

    public function getRequestHistory($id)
    {
        $data = InventoryRequests::with(['mastercoach', 'coach', 'items.inventory'])->find($id);

        if (!$data) {
            return $this->ErrorResponse('Data peminjaman tidak ditemukan.', 404);
        }

        // Flatten data menjadi satu dimensi kecuali untuk 'items'
        $flattened = [
            'id' => $data->id,
            'status' => $data->status,
            'tanggal_pinjam' => $data->tanggal_pinjam,
            'tanggal_kembali' => $data->tanggal_kembali,
            'alasan_pinjam' => $data->alasan_pinjam,
            'mastercoach_id' => $data->mastercoach_id,
            'mastercoach_name' => optional($data->mastercoach)->name,
            'coach_id' => $data->coach_id,
            'coach_name' => optional($data->coach)->name,
            'rejection_reason' => $data->rejection_reason,
            'created_at' => $data->created_at,
        ];

        $flattened['items'] = $data->items->map(function ($item) {
            return [
                'inventory_id' => $item->inventory_id,
                'inventory_name' => optional($item->inventory)->name,
                'qty_requested' => $item->qty_requested
            ];
        })->toArray();

        return $this->SuccessResponse($flattened, 'Detail peminjaman berhasil diambil.');
    }

    public function getReturnHistory($id)
    {
        $data = InventoryReturns::with(['mastercoach', 'coach', 'inventory', 'landing', 'request'])->find($id);

        if (!$data) {
            return $this->ErrorResponse('Data pengembalian tidak ditemukan.', 404);
        }

        $flattened = [
            'id' => $data->id,
            'status' => $data->status,
            'qty_returned' => $data->qty_returned,
            'returned_at' => $data->returned_at,
            'inventory_id' => $data->inventory_id,
            'inventory_name' => optional($data->inventory)->name,
            'mastercoach_id' => $data->mastercoach_id,
            'mastercoach_name' => optional($data->mastercoach)->name,
            'coach_id' => $data->coach_id,
            'coach_name' => optional($data->coach)->name,
            'landing_id' => $data->inventory_landing_id,
            'landing_tanggal_pinjam' => optional($data->landing)->tanggal_pinjam,
            'landing_tanggal_kembali' => optional($data->landing)->tanggal_kembali,
            'landing_alasan_pinjam' => optional($data->landing)->alasan_pinjam,
            'request_id' => optional($data->landing)->request_id,
            'request_tanggal_pinjam' => optional($data->landing->request)->tanggal_pinjam,
            'request_tanggal_kembali' => optional($data->landing->request)->tanggal_kembali,
            'rejection_reason' => $data->rejection_reason,
            'created_at' => $data->created_at,
        ];

        return $this->SuccessResponse($flattened, 'Detail pengembalian berhasil diambil.');
    }

    public function getList()
    {

        $userId = Auth::id();

        $inventory = DB::table('inventories')
            ->leftJoin('inventory_landings', function ($join) use ($userId) {
                $join->on('inventories.id', '=', 'inventory_landings.inventory_id')
                    ->where('inventory_landings.status', 'borrowed')
                    ->where('inventory_landings.coach_id', $userId);
            })
            ->leftJoin('inventory_returns', function ($join) {
                $join->on('inventory_landings.id', '=', 'inventory_returns.inventory_landing_id')
                    ->where('inventory_returns.status', 'approved');
            })
            ->select(
                'inventories.id as inventory_id',
                'inventories.name',
                'inventories.inventory_image',
                DB::raw('COALESCE(SUM(inventory_landings.qty_borrowed), 0) as total_qty_borrowed'),
                DB::raw('COALESCE(SUM(inventory_returns.qty_returned), 0) as total_qty_returned'),
                DB::raw('(COALESCE(SUM(inventory_landings.qty_borrowed), 0) - COALESCE(SUM(inventory_returns.qty_returned), 0)) as total_qty_remaining')
            )
            ->groupBy('inventories.id', 'inventories.name', 'inventories.inventory_image')
            ->having('total_qty_remaining', '>', 0)
            ->get()
            ->map(function ($item) {
                $item->inventory_image_url = url('storage/' . $item->inventory_image);
                return $item;
            });

        return $this->SuccessResponse($inventory, 'Data peminjaman berhasil diambil.');
    }

    public function getListDetail($inventoryId)
    {
        $userId = Auth::id();

        if (!$userId) {
            return $this->ErrorResponse('Unauthorized', 401);
        }

        // Ambil data peminjaman
        $peminjaman = InventoryLandings::with(['coach', 'mastercoach', 'inventory'])
            ->where('coach_id', $userId)
            ->where('inventory_id', $inventoryId)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'peminjaman',
                    'id' => $item->id,
                    'created_at' => $item->created_at,
                    'tanggal_pinjam' => $item->tanggal_pinjam,
                    'tanggal_kembali' => $item->tanggal_kembali,
                    'status' => $item->status,
                    'qty' => $item->qty_borrowed,
                    'coach_name' => $item->coach->name ?? null,
                    'mastercoach_name' => $item->mastercoach->name ?? null,
                    'inventory_name' => $item->inventory->name ?? null,
                ];
            });

        // Ambil data pengembalian
        $pengembalian = InventoryReturns::with(['coach', 'mastercoach', 'inventory'])
            ->where('coach_id', $userId)
            ->where('inventory_id', $inventoryId)
            ->get()
            ->map(function ($ret) {
                return [
                    'type' => 'pengembalian',
                    'id' => $ret->id,
                    'created_at' => $ret->created_at,
                    'returned_at' => $ret->returned_at,
                    'status' => $ret->status,
                    'qty' => $ret->qty_returned,
                    'damaged_count' => $ret->damaged_count,
                    'missing_count' => $ret->missing_count,
                    'img_inventory_return' => $ret->img_inventory_return
                        ? url('storage/' . $ret->img_inventory_return)  // Menambahkan URL gambar
                        : null,  // Jika tidak ada gambar, set null
                    'rejection_reason' => $ret->rejection_reason,
                    'desc' => $ret->desc,
                    'coach_name' => $ret->coach->name ?? null,
                    'mastercoach_name' => $ret->mastercoach->name ?? null,
                    'inventory_name' => $ret->inventory->name ?? null,
                ];
            });

        // Gabung dan urutkan berdasarkan created_at dari yang terbaru
        $merged = $peminjaman->concat($pengembalian)->sortByDesc('created_at')->values();

        if ($merged->isEmpty()) {
            return $this->ErrorResponse('Data tidak ditemukan.', 404);
        }

        return $this->SuccessResponse($merged, 'Riwayat peminjaman dan pengembalian berhasil diambil.');
    }

    public function getListStuffInventory($mastercoachId){
        $inventory = InventoryManagement::with(['mastercoach', 'inventory'])
            ->where('mastercoach_id', $mastercoachId)
            ->get();

        if ($inventory->isEmpty()) {
            return $this->ErrorResponse('Data inventory tidak ditemukan.', 404);
        }

        $data = $inventory->map(function ($item) {
            return [
                'id' => $item->id,
                'mastercoach_id' => $item->mastercoach_id,
                'inventory_id' => $item->inventory_id,
                'qty' => $item->qty,
                'mastercoach_name' => $item->mastercoach->name ?? null,
                'inventory_name' => $item->inventory->name ?? null,
            ];
        });

        return $this->SuccessResponse($data, 'Data inventory berhasil diambil.');
    }

    public function getListMasterCoach(){
        $mastercoaches = InventoryManagement::with('mastercoach')
            ->select('mastercoach_id')
            ->distinct()
            ->get();

        if ($mastercoaches->isEmpty()) {
            return $this->ErrorResponse('Data mastercoach tidak ditemukan.', 404);
        }

        $data = $mastercoaches->map(function ($item) {
            return [
                'id' => $item->mastercoach->id,
                'name' => $item->mastercoach->name,
            ];
        });

        return $this->SuccessResponse($data, 'Data mastercoach berhasil diambil.');
    }

    public function getDetailInventoryReturn($landingId){
        $userId = Auth::id();

        if (!$userId) {
            return $this->ErrorResponse('Unauthorized', 401);
        }

        $inventory_landing = InventoryLandings::with(['inventory', 'coach', 'mastercoach'])
            ->where('id', $landingId)
            ->first();

        if (!$inventory_landing) {
            return $this->ErrorResponse('Data peminjaman tidak ditemukan.', 404);
        }

        $data = [
            'id' => $inventory_landing->id,
            'coach_id' => $inventory_landing->coach_id,
            'coach_name' => $inventory_landing->coach->name ?? null,
            'mastercoach_id' => $inventory_landing->mastercoach_id,
            'mastercoach_name' => $inventory_landing->mastercoach->name ?? null,
            'tanggal_pinjam' => $inventory_landing->tanggal_pinjam,
            'inventory_id' => $inventory_landing->inventory_id,
            'inventory_name' => $inventory_landing->inventory->name ?? null,
            'qty_borrowed' => $inventory_landing->qty_borrowed
        ];

        return $this->SuccessResponse($data, 'Detail peminjaman berhasil diambil.');
    }

}
