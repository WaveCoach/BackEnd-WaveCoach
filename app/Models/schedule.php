<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class schedule extends Model
{
    protected $table = 'schedules';

    protected $fillable = [
        'coach_id',
        'location_id',
        'date',
        'start_time',
        'end_time',
    ];
}
