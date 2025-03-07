<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'inventory_id',
        'qty_requested',
    ];

    public function request()
    {
        return $this->belongsTo(InventoryRequests::class, 'request_id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
