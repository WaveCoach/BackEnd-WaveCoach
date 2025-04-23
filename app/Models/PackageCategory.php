<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Calculation\Category;

class PackageCategory extends Model
{
    protected $table = 'package_category';

    protected $fillable = [
        'category_id',
        'package_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
