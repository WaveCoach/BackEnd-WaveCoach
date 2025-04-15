<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLandings extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'mastercoach_id',
        'coach_id',
        'request_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'qty_borrowed',
        'qty_returned',
        'qty_pending_return',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function mastercoach()
    {
        return $this->belongsTo(User::class, 'mastercoach_id');
    }

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function request()
    {
        return $this->belongsTo(InventoryRequests::class, 'request_id');
    }

    public function returns()
    {
        return $this->hasMany(InventoryReturns::class, 'inventory_landing_id');
    }
}
