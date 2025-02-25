<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class inventory_management extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'inventory_management';

    protected $fillable = [
        'mastercoach_id',
        'inventory_id',
        'qty'
    ];

    protected $dates = ['deleted_at'];

    public function mastercoach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mastercoach_id');
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
