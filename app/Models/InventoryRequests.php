<?php

namespace App\Models;

use App\Observers\InventoryRequestObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class InventoryRequests extends Model
{
    protected $fillable = [
        'mastercoach_id',
        'coach_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'alasan_pinjam',
        'status',
        'rejection_reason',
    ];

    public function mastercoach()
    {
        return $this->belongsTo(User::class, 'mastercoach_id');
    }

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function items()
    {
        return $this->hasMany(InventoryRequestItem::class, 'request_id');
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    protected static function booted()
    {
        static::observe(InventoryRequestObserver::class);
    }
}
