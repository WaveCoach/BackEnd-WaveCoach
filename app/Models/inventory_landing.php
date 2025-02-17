<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class inventory_landing extends Model
{
    protected $table = 'inventory_transactions';

    protected $fillable = [
        'inventory_id',
        'borrower_id',
        'borrowed_to_id',
        'inventory_quantity',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function borrower()
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function borrowedTo()
    {
        return $this->belongsTo(User::class, 'borrowed_to_id');
    }
}
