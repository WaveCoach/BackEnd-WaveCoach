<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentAspect extends Model
{
    protected $table = 'assessment_aspects';

    protected $fillable = [
        'assesment_categories_id',
        'name',
    ];

    public function category()
    {
        return $this->belongsTo(AssessmentCategory::class, 'assesment_categories_id');
    }
}
