<?php

namespace App\Imports;

use App\Models\Location;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LocationImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $existingLocation = Location::where('name', $row['name'])->first();
        if ($existingLocation) {
            return null;
        }

        return new Location([
            'name'      => $row['name'] ?? null,
            'address'   => $row['address'] ?? null,
            'maps'      => $row['maps'] ?? null,
            'code_loc'  => $this->generateUniqueCode(),
        ]);
    }

    private function generateUniqueCode()
    {
        do {
            $code = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (Location::where('code_loc', $code)->exists());

        return $code;
    }
}
