<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class RescheduleRequest extends Model
{
    use HasFactory;

    protected $fillable = ['schedule_id', 'coach_id', 'requested_date', 'requested_time', 'reason', 'status', 'admin_id', 'response_message'];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function students(): HasManyThrough
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

    public function scheduleDetails(): HasMany
    {
        return $this->hasMany(ScheduleDetail::class, 'schedule_id');
    }

    public function rescheduleRequests(): HasMany
    {
        return $this->hasMany(RescheduleRequest::class, 'schedule_id');
    }
}
