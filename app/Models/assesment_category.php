<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class assesment_category extends Model
{
    protected $table = 'assesment_categories';

    protected $fillable = [
        'name',
    ];

    public function aspects()
    {
        return $this->hasMany(assesment_aspect::class, 'assesment_categories_id');
    }




}
