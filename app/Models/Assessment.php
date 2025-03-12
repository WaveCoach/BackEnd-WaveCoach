<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $table = 'assessments';

    protected $fillable = [
        'student_id',  'assessor_id', 'assessment_date', 'package_id', 'assessement_category_id'
    ];
}
