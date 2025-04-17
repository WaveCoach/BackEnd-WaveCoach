<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coaches extends Model
{
    protected $table = "coaches";
    protected $fillable = ["user_id", "status", "tanggal_bergabung"];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
