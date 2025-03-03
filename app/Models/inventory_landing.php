<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class inventory_landing extends Model
{
    protected $table = 'inventory_management';

    protected $fillable = [
        'inventory_landings_id',
        'inventory_id',
        'mastercoach_id',
        'coach_id',
        'qty_in',
        'qty_out'
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
