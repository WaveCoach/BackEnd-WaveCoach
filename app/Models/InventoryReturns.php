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
        'img_inventory_return',
        'status',
        'damaged_count',
        'missing_count',
        'rejection_reason',
        'desc',

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

    public function request()
    {
        return $this->hasOneThrough(
            \App\Models\InventoryRequests::class,
            \App\Models\InventoryLandings::class,
            'id', // id di InventoryLandings
            'id', // id di InventoryRequests
            'inventory_landing_id', // foreign key di InventoryReturns
            'request_id' // foreign key di InventoryLandings
        );
    }



}
