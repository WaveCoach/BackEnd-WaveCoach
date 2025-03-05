<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentAspect extends Model
{
    protected $table = 'assesment_aspects';

    protected $fillable = [
        'assesment_categories_id',
        'name',
    ];

    public function category()
    {
        return $this->belongsTo(assesment_category::class, 'assesment_categories_id');
    }
}
