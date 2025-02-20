<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class schedule_detail extends Model
{
    protected $table = 'schedule_details';

    protected $fillable = ['schedule_id', 'user_id'];
}
