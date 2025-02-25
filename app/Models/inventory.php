<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class inventory extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'inventories';

    protected $fillable = [
        'name',
        'total_quantity',
    ];

    public function inventoryManagements(): HasMany
    {
        return $this->hasMany(inventory_management::class, 'inventory_id')->withTrashed();
    }
}
