<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageStudent extends Model
{
    protected $table = 'package_student';

    protected $fillable = [
        'student_id',
        'package_id'
    ];

    public function student()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke model Package
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
