<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\InventoryLanding;
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

        DB::beginTransaction();

        try {
            $loanRequest = InventoryRequests::create([
                'mastercoach_id'  => $request->mastercoach_id,
                'coach_id'        => Auth::user()->id,
                'tanggal_pinjam'  => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'alasan_pinjam'   => $request->alasan_pinjam,
                'status'          => 'pending',
            ]);

            foreach ($request->items as $item) {
                $inventory = InventoryManagement::where('mastercoach_id', $request->mastercoach_id)
                    ->where('inventory_id', $item['inventory_id'])
                    ->first();

                if (!$inventory || $inventory->qty < $item['qty_requested']) {
                    throw new \Exception("Stok tidak cukup untuk barang dengan ID {$item['inventory_id']}");
                }

                InventoryRequestItem::create([
                    'request_id'   => $loanRequest->id,
                    'inventory_id' => $item['inventory_id'],
                    'qty_requested' => $item['qty_requested'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Permintaan peminjaman berhasil diajukan!',
                'request' => $loanRequest->load('items'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal mengajukan peminjaman!',
                'error'   => $e->getMessage(),
            ], 400);
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
                return response()->json([
                    'message' => 'Permintaan peminjaman sudah diproses sebelumnya!',
                ], 400);
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
                        'mastercoach_id' => Auth::user()->id,
                        'tanggal_pinjam' => $loanRequest->tanggal_pinjam,
                        'tanggal_kembali' => $loanRequest->tanggal_kembali,
                        'qty_out' => $item->qty_requested,
                        'status'       => 'borrowed',
                    ]);
                }

                $loanRequest->update(['status' => 'approved']);
            } elseif ($request->status === 'rejected') {
                $loanRequest->update([
                    'status' => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => "Permintaan peminjaman telah {$request->status}!",
                'request' => $loanRequest,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal memperbarui status peminjaman!',
                'error'   => $e->getMessage(),
            ], 400);
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
                return response()->json(['message' => 'Barang belum dipinjam atau sudah dikembalikan!'], 400);
            }

            if ($request->qty_returned > $landing->qty_out) {
                return response()->json(['message' => 'Jumlah yang dikembalikan lebih dari yang dipinjam!'], 400);
            }

            $return = InventoryReturns::create([
                'inventory_landing_id' => $landing->id,
                'inventory_id'         => $landing->inventory_id,
                'mastercoach_id'       => $landing->mastercoach_id,
                'coach_id'             => $landing->coach_id,
                'qty_returned'         => $request->qty_returned,
                'returned_at'          => now(),
            ]);

            $inventory = InventoryManagement::where('inventory_id', $landing->inventory_id)
                ->where('mastercoach_id', $landing->mastercoach_id)
                ->first();

            if ($inventory) {
                $inventory->increment('qty', $request->qty_returned);
            }

            if ($request->qty_returned >= $landing->qty_out) {
                $landing->update(['status' => 'returned']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Barang berhasil dikembalikan!',
                'return'  => $return,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal mengembalikan barang!', 'error' => $e->getMessage()], 400);
        }
    }

}
