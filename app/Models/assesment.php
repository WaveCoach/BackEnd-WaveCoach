<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class assesment extends Model
{
    protected $table = 'assesments';

    protected $fillable = [
        'user_id',  'assessor_id', 'assessor_id'
    ];
}
