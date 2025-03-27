<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\InventoryManagement;
use Illuminate\Http\Request;

class InventoryManagementController extends BaseController
{
    public function index()
    {
        $inventoryByCoach = InventoryManagement::with(['mastercoach', 'inventory'])
        ->select('mastercoach_id', 'inventory_id')
        ->selectRaw('SUM(qty) as total_qty')
        ->groupBy('mastercoach_id', 'inventory_id')
        ->get()
        ->groupBy('mastercoach_id');

    $response = $inventoryByCoach->map(function ($inventories, $mastercoachId) {
        $mastercoach = $inventories->first()->mastercoach;

        return [
            'mastercoach_id' => $mastercoachId,
            'mastercoach_name' => $mastercoach->name ?? 'Unknown',
            'items' => $inventories
                ->filter(fn($item) => $item->total_qty > 0) // Hanya ambil yang total_qty > 0
                ->map(function ($item) {
                    return [
                        'inventory_id' => $item->inventory_id,
                        'inventory_name' => $item->inventory->name ?? 'Unknown',
                        'total_qty' => $item->total_qty
                    ];
                })
                ->values()
        ];
    })->filter(fn($data) => count($data['items']) > 0) // Hilangkan mastercoach yang tidak punya items
    ->values();

return $this->SuccessResponse($response, 'Data inventory berhasil diambil');

    }
}
