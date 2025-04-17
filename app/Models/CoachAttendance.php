<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'coach_id',
        'attendance_status',
        'remarks', //alasan
        'proof', //gambar bukti
    ];

    // Relasi ke model Coach
    public function coach()
    {
        return $this->belongsTo(user::class,'coach_id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    // Accessor untuk proof
    public function getProofAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }
}
