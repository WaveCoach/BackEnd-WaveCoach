<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\InventoryLanding;
use App\Models\InventoryManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends BaseController
{
    public function borrowInventory(Request $request)
    {
        DB::beginTransaction();

        try {
            $dataPeminjaman = $request->input('items');
            $mastercoachId = $request->input('mastercoach_id');
            $coachId = $request->input('coach_id');
            $tanggalPinjam = $request->input('tanggal_pinjam');
            $tanggalKembali = $request->input('tanggal_kembali');
            $alasanPinjam = $request->input('alasan_pinjam');

            foreach ($dataPeminjaman as $item) {
                $inventoryId = $item['inventory_id'];
                $qtyOut = $item['qty_out'];

                $inventory = InventoryManagement::where('mastercoach_id', $mastercoachId)
                    ->where('inventory_id', $inventoryId)
                    ->first();

                if (!$inventory || $inventory->qty < $qtyOut) {
                    throw new \Exception("Stok tidak cukup untuk barang dengan ID $inventoryId");
                }

                $inventory->decrement('qty', $qtyOut);

                InventoryLanding::create([
                    'inventory_id' => $inventoryId,
                    'mastercoach_id' => $mastercoachId,
                    'coach_id' => $coachId,
                    'status' => 'dipinjam',
                    'tanggal_pinjam' => $tanggalPinjam,
                    'tanggal_kembali' => $tanggalKembali,
                    'alasan_pinjam' => $alasanPinjam,
                    'qty_out' => $qtyOut,
                ]);
            }

            DB::commit();
            return $this->SuccessResponse([], 'Peminjaman berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->ErrorResponse($e->getMessage(), 400);
        }
    }
}
