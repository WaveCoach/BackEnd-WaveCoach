<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
