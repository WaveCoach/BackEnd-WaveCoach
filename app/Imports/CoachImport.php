<?php

namespace App\Imports;

use App\Models\Coaches;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CoachImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Cek apakah user sudah ada
        if (User::where('email', $row['email'] ?? null)->exists()) {
            return null;
        }

        // Buat user baru
        $user = User::create([
            'name'     => $row['name'] ?? null,
            'email'    => $row['email'] ?? null,
            'password' => bcrypt('12345678'),
            'role_id'  => 2,
        ]);

        // Konversi tanggal
        $tanggal = null;
        if (!empty($row['tanggal_bergabung'])) {
            try {
                $tanggal = Carbon::createFromFormat('d/m/Y', $row['tanggal_bergabung'])->format('Y-m-d');
            } catch (\Exception $e) {
                // Tanggal gagal diformat, bisa di-log atau diabaikan
            }
        }

        // Return langsung instance Coach
        return new Coaches([
            'user_id'          => $user->id,
            'tanggal_bergabung' => $tanggal,
        ]);
    }
}
