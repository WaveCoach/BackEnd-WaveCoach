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
        return Location::select('id', 'name', 'address', 'code_loc', 'maps') // Pilih kolom yang ingin diekspor
        ->whereNull('deleted_at') // Ambil hanya data yang belum dihapus
        ->get();

    }
}
