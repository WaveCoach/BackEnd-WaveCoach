<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'student_id'
    ];

    public function student()
    {
        return $this->belongsTo(user::class,'student_id');
    }
}
