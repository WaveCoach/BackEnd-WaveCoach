<?php

namespace App\Imports;

use App\Models\Coaches;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

class CoachImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (User::where('email', $row[1])->exists()) {
            return null;
        }

        $user = User::create([
            'name' => $row[0],
            'email' => $row[1],
            'password' => bcrypt('12345678'),
            'role_id' => 2,
        ]);

        Coaches::create([
            'user_id' => $user->id,
            'tanggal_bergabung' => $row[2],
        ]);

        return $user;
    }
}
