<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'student_id',
        'attendance_status'
    ];

    public function student()
    {
        return $this->belongsTo(User::class,'student_id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class,'schedule_id');
    }
}
