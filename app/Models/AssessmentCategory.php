<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentCategory extends Model
{
    protected $table = 'assessment_categories';

    protected $fillable = [
        'name', 'kkm'
    ];

    public function aspects()
    {
        return $this->hasMany(AssessmentAspect::class, 'assessment_categories_id');
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_category', 'category_id', 'package_id');
    }
}
