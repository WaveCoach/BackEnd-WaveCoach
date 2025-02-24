<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class inventory_management extends Model
{
    protected $table = 'inventory_management';

    protected $fillable = [
        'mastercoach_id',
        'inventory_id',
        'qty'
    ];
}
