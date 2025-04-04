<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentCategory extends Model
{
    protected $table = 'assessment_categories';

    protected $fillable = [
        'name',
    ];

    public function aspects()
    {
        return $this->hasMany(AssessmentAspect::class, 'assessment_categories_id');
    }
}
