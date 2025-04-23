<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageCoach extends Model
{
    protected $table = 'package_coach';

    protected $fillable = [
        'coach_id',
        'package_id',
    ];

    public function coach()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
