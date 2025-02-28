<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class assessments_detail extends Model
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
        return $this->belongsTo(assesment::class, 'assessment_id');
    }

    public function aspect()
    {
        return $this->belongsTo(assesment_aspect::class, 'aspect_id');
    }
}
