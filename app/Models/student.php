<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class student extends Model
{
    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'tanggal_lahir',
        'nis',
        'jenis_kelamin',
        'type'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
