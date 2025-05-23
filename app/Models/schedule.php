<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Schedule extends Model
{
    protected $table = 'schedules';

    protected $fillable = [
        'coach_id',
        'location_id',
        'date',
        'start_time',
        'end_time',
        'package_id',
        'status',
        'is_assessed',
    ];


    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function students()
    {
        return $this->hasManyThrough(
            User::class,
            ScheduleDetail::class,
            'schedule_id',
            'id',
            'id',
            'user_id'
        );
    }

    public function scheduleDetail() {
        return $this->hasMany(ScheduleDetail::class, 'schedule_id');
    }

    public function rescheduleRequests(): HasMany
    {
        return $this->hasMany(RescheduleRequest::class, 'schedule_id');
    }


}
