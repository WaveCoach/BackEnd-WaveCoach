<?php

namespace App\Exports;

use App\Models\Location;
use Maatwebsite\Excel\Concerns\FromCollection;

class LocationExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Location::select('name', 'address', 'code_loc', 'maps')
            ->whereNull('deleted_at')
            ->get();
    }

    public function headings(): array
    {
        return ["Name", "Address", "Code Loc", "Maps"]; // Header di Excel
    }
}
