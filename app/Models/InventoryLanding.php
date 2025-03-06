<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLanding extends Model
{
    protected $table = 'inventory_landings';

    protected $fillable = [
        'inventory_landings_id',
        'inventory_id',
        'mastercoach_id',
        'coach_id',
        'status',
        'qty_in',
        'qty_out',
        'tanggal_pinjam',
        'tanggal_kembali',
        'tanggal_dikembalikan',
        'alasan_pinjam'
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function mastercoach()
    {
        return $this->belongsTo(User::class, 'mastercoach_id');
    }
}
