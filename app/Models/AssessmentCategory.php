<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentCategory extends Model
{
    protected $table = 'assesment_categories';

    protected $fillable = [
        'name',
    ];

    public function aspects()
    {
        return $this->hasMany(AssessmentAspect::class, 'assesment_categories_id');
    }
}
