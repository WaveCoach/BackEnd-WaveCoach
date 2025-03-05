<?php

namespace App\Exports;

use App\Models\location;
use Maatwebsite\Excel\Concerns\FromCollection;

class LocationExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return location::all();
    }
}
