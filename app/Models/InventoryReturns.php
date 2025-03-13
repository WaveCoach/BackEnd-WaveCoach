<?php

namespace App\Models;

use App\Observers\InventoryReturnObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class InventoryReturns extends Model
{
    protected $fillable = [
        'inventory_landing_id',
        'inventory_id',
        'mastercoach_id',
        'coach_id',
        'qty_returned',
        'returned_at',
        'status',
        'rejection_reason'
    ];

    public function landing()
    {
        return $this->belongsTo(InventoryLandings::class, 'inventory_landing_id');
    }

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

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    // protected static function booted()
    // {
    //     static::observe(InventoryReturnObserver::class);
    // }
}
