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

        // Cek apakah sudah ada permintaan di hari yang sama dengan mastercoach dan coach yang sama
        $existingRequest = InventoryRequests::where('mastercoach_id', $request->mastercoach_id)
        ->where('coach_id', Auth::id())
        ->whereDate('tanggal_pinjam', $request->tanggal_pinjam)
        ->where('status', 'pending')
        ->exists();

        if ($existingRequest) {
            return $this->ErrorResponse(
                'Anda sudah mengajukan permintaan untuk tanggal ini. Silakan ajukan lagi besok!',
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

            $loanRequest->notifications()->create([
                'user_id'    => $loanRequest->mastercoach_id, // Mastercoach yang akan menerima notifikasi
                'pengirim_id' => $loanRequest->coach_id,
                'title'      => 'Permintaan Peminjaman Barang',
                'message'    => "Peminjaman oleh {$loanRequest->coach->name} telah diajukan untuk barang tertentu.",
                'type'       => 'request',
                'is_read'    => false,
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
                        'qty_out' => $item->qty_requested,
                        'status'  => 'borrowed',
                    ]);
                }

                $loanRequest->update(['status' => 'approved']);

                $loanRequest->notifications()->create([
                    'pengirim_id' => $loanRequest->mastercoach_id,
                    'user_id'  => $loanRequest->coach_id, // Coach yang mengajukan peminjaman
                    'title'    => 'Peminjaman Disetujui',
                    'message'  => "Peminjaman barang Anda telah disetujui oleh Mastercoach.",
                    'type'     => 'approval',
                    'is_read'  => 0,
                ]);

            } elseif ($request->status === 'rejected') {
                $loanRequest->update([
                    'status' => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                ]);

                $loanRequest->notifications()->create([
                    'pengirim_id' => $loanRequest->mastercoach_id,
                    'user_id'  => $loanRequest->coach_id, // Coach yang mengajukan peminjaman
                    'title'    => 'Peminjaman Ditolak',
                    'message'  => "Permintaan peminjaman Anda ditolak. Alasan: {$request->rejection_reason}",
                    'type'     => 'rejection',
                    'is_read'  => 0,
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
        $request->validate([
            'qty_returned' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $landing = InventoryLandings::findOrFail($landingId);

            if ($landing->status !== 'borrowed') {
                return $this->ErrorResponse('Barang belum dipinjam atau sudah dikembalikan!', 400);
            }

            if ($request->qty_returned > $landing->qty_out) {
                return $this->ErrorResponse('Jumlah yang dikembalikan lebih dari yang dipinjam!', 400);
            }

            // Cek apakah sudah ada pengajuan pengembalian yang masih pending untuk barang ini
            $existingReturn = InventoryReturns::where('inventory_landing_id', $landing->id)
                ->where('status', 'pending')
                ->exists();

            if ($existingReturn) {
                return $this->ErrorResponse('Pengajuan pengembalian untuk barang ini sudah dibuat dan masih menunggu persetujuan!', 400);
            }

            // Buat pengajuan pengembalian dengan status 'pending'
            $return = InventoryReturns::create([
                'inventory_landing_id' => $landing->id,
                'inventory_id'         => $landing->inventory_id,
                'mastercoach_id'       => $landing->mastercoach_id,
                'coach_id'             => $landing->coach_id,
                'qty_returned'         => $request->qty_returned,
                'returned_at'          => now(),
                'status'               => 'pending', // Pengembalian menunggu persetujuan
            ]);

            $return->notifications()->create([
                'pengirim_id' => $landing->coach_id,
                'user_id'  => $landing->mastercoach_id, // Mastercoach sebagai penerima notifikasi
                'title'    => 'Pengajuan Pengembalian Barang',
                'message'  => "Coach {$landing->coach_id} telah mengajukan pengembalian barang ID {$landing->inventory_id}.",
                'type'     => 'return_request',
                'is_read'  => 0,
            ]);

            DB::commit();

            return $this->SuccessResponse($return, 'Pengajuan pengembalian berhasil dibuat, menunggu persetujuan mastercoach.', 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->ErrorResponse('Gagal mengajukan pengembalian!', 400, ['error' => $e->getMessage()]);
        }
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

            if ($request->status === 'approved') {
                $landing = InventoryLandings::findOrFail($returnRequest->inventory_landing_id);

                if ($landing->qty_out < $returnRequest->qty_returned) {
                    throw new \Exception("Jumlah qty_out tidak mencukupi untuk dikembalikan.");
                }

                $landing->increment('qty_remaining', $returnRequest->qty_returned);
                $landing->decrement('qty_out', $returnRequest->qty_returned);


                if ($landing->qty_out == 0) {
                    $landing->update(['status' => 'returned']);
                }

                $inventory = InventoryManagement::where('mastercoach_id', $returnRequest->mastercoach_id)
                    ->where('inventory_id', $returnRequest->inventory_id)
                    ->first();
                $inventory->increment('qty', $returnRequest->qty_returned);
                $returnRequest->updateOrFail(['status' => 'approved']);
                $returnRequest->notifications()->create([
                    'pengirim_id' => $returnRequest->mastercoach_id,
                    'user_id'  => $returnRequest->coach_id,
                    'title'    => 'Pengembalian Barang Disetujui',
                    'message'  => "Pengembalian barang telah disetujui oleh Mastercoach.",
                    'type'     => 'return_approved',
                    'is_read'  => 0,
                ]);

            } elseif ($request->status === 'rejected') {
                $returnRequest->updateOrFail([
                    'status' => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                ]);

                $returnRequest->notifications()->create([
                    'pengirim_id' => $returnRequest->mastercoach_id,
                    'user_id'  => $returnRequest->coach_id, // Coach yang mengajukan
                    'title'    => 'Pengembalian Barang Ditolak',
                    'message'  => "Pengembalian barang ditolak. Alasan: {$request->rejection_reason}.",
                    'type'     => 'return_rejected',
                    'is_read'  => 0,
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
                'users.name AS coach_name'
            )
            ->leftJoin('users', 'users.id', '=', 'inventory_requests.coach_id');

        $returnsQuery = DB::table('inventory_returns')
            ->select(
                'inventory_returns.id',
                'inventory_returns.mastercoach_id',
                'inventory_returns.coach_id',
                'inventory_returns.status',
                'inventory_returns.created_at',
                'inventory_returns.updated_at',
                DB::raw("'return' AS type"),
                'users.name AS coach_name'
            )
            ->leftJoin('users', 'users.id', '=', 'inventory_returns.coach_id');

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

        $inventory = $requestsQuery->union($returnsQuery)->get();

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
        ];

        return $this->SuccessResponse($flattened, 'Detail pengembalian berhasil diambil.');
    }


    public function getList()
    {
        $inventory = Inventory::leftJoin('inventory_landings', function ($join) {
                $join->on('inventories.id', '=', 'inventory_landings.inventory_id')
                    ->where('inventory_landings.status', 'borrowed')
                    ->where('inventory_landings.coach_id', Auth::id());
            })
            ->select(
                'inventories.id as inventory_id',
                'inventories.name',
                DB::raw('COALESCE(SUM(inventory_landings.qty_out), 0) as total_qty_borrowed')
            )
            ->groupBy('inventories.id', 'inventories.name')
            ->get()
            ->filter(fn($item) => $item->total_qty_borrowed > 0)
            ->values();

        return $this->SuccessResponse($inventory, 'Data peminjaman berhasil diambil.');
    }


    public function getListDetail($inventoryId)
    {
        $userId = Auth::id();

        if (!$userId) {
            return $this->ErrorResponse('Unauthorized', 401);
        }

        $inventory_landing = InventoryLandings::with(['coach', 'mastercoach', 'inventory'])
            ->where('coach_id', $userId)
            ->where('inventory_id', $inventoryId)
            ->get();


        if ($inventory_landing->isEmpty()) {
            return $this->ErrorResponse('Data peminjaman tidak ditemukan.', 404);
        }

        $data = $inventory_landing->map(function ($item) {
            return [
                'id' => $item->id,
                'tanggal_pinjam' => $item->tanggal_pinjam,
                'tanggal_kembali' => $item->tanggal_kembali,
                'status' => $item->status,
                'qty_out' => $item->qty_out,
                'qty_remaining' => $item->qty_remaining,
                'coach_name' => $item->coach->name ?? null,
                'mastercoach_name' => $item->mastercoach->name ?? null,
                'inventory_name' => $item->inventory->name ?? null,
            ];
        });

        return $this->SuccessResponse($data, 'Data peminjaman berhasil diambil.');
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

}
