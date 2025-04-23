<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = "packages";
    protected $fillable = ["name", "desc"];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'package_student', 'package_id', 'student_id');
    }
}
