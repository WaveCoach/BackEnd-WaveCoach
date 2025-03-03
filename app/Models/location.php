<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class location extends Model
{
    protected $table = 'locations';

    protected $fillable = [
        'name',
        'address',
        'maps',
        'code_loc'
    ];

    protected $dates = ['deleted_at'];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'location_id');
    }



}
