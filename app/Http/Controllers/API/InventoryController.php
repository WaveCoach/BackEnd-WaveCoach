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

            DB::commit();

            return response()->json([
                'message' => 'Pengajuan pengembalian berhasil dibuat, menunggu persetujuan mastercoach.',
                'return'  => $return,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal mengajukan pengembalian!', 'error' => $e->getMessage()], 400);
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
                return response()->json(['message' => 'Pengembalian sudah diproses sebelumnya!'], 400);
            }

            if ($request->status === 'approved') {
                $landing = InventoryLandings::findOrFail($returnRequest->inventory_landing_id);

                if ($landing->qty_out < $returnRequest->qty_returned) {
                    throw new \Exception("Jumlah qty_out tidak mencukupi untuk dikembalikan.");
                }

                $landing->decrement('qty_out', $returnRequest->qty_returned);

                // Jika semua barang telah dikembalikan, ubah statusnya
                if ($landing->qty_out == 0) {
                    $landing->update(['status' => 'returned']);
                }

                $inventory = InventoryManagement::where('mastercoach_id', $returnRequest->mastercoach_id)
                    ->where('inventory_id', $returnRequest->inventory_id)
                    ->firstOrFail(); // Lebih baik menggunakan firstOrFail()

                $inventory->increment('qty', $returnRequest->qty_returned);

                $returnRequest->updateOrFail(['status' => 'approved']);

            } elseif ($request->status === 'rejected') {
                $returnRequest->updateOrFail([
                    'status' => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => "Pengembalian telah {$request->status}!",
                'return' => $returnRequest,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal memperbarui status pengembalian!',
                'error' => $e->getMessage()
            ], 400);
        }
    }


}
