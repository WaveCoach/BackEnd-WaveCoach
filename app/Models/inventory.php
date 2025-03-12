<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventories';

    protected $fillable = [
        'name',
        'inventory_image'
    ];

    public function inventoryManagements(): HasMany
    {
        return $this->hasMany(InventoryManagement::class, 'inventory_id');
    }
}
