<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'tanggal_lahir',
        'nis',
        'jenis_kelamin',
        'type'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function packageStudents()
    {
        return $this->hasMany(PackageStudent::class);
    }

    public function packages() {
        return $this->belongsToMany(Package::class, 'package_student', 'student_id', 'package_id');
    }

}
