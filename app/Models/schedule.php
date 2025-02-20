<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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


    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function students()
    {
        return $this->hasManyThrough(
            User::class,
            schedule_detail::class,
            'schedule_id',
            'id',
            'id',
            'user_id'
        );
    }

}
