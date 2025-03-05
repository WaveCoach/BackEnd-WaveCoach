<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentDetail extends Model
{
    protected $table = 'assessments_details';

    protected $fillable = [
       'assessment_id',
       'aspect_id',
       'score',
       'remarks',
    ];

   public function assessment()
   {
       return $this->belongsTo(Assessment::class, 'assessment_id');
   }

   public function aspect()
   {
       return $this->belongsTo(AssessmentAspect::class, 'aspect_id');
   }
}
