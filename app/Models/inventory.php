<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class inventory extends Model
{
    protected $table = 'inventories';

    protected $fillable = [
        'name',
        'total_quantity',
    ];
}
