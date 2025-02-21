<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class assesment extends Model
{
    protected $table = 'assessments';

    protected $fillable = [
        'user_id',  'assessor_id', 'assesment_date'
    ];
}
