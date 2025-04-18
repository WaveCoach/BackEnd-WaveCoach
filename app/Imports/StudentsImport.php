<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class StudentsImport implements ToModel, WithHeadingRow, WithMapping
{
    /**
     * Map the row to a consistent format.
     */
    public function map($row): array
    {
        return [
            'name'              => $row['name'] ?? null,
            'email'             => $row['email'] ?? null,
            'jenis_kelamin'     => $row['jenis_kelamin'] ?? null,
            'tanggal_lahir'     => $this->parseTanggal($row['tanggal_lahir'] ?? null),
            'tanggal_bergabung' => $this->parseTanggal($row['tanggal_bergabung'] ?? null),
        ];
    }

    /**
     * Convert Excel date format or string to Y-m-d.
     */
    private function parseTanggal($value)
    {
        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            } elseif (is_string($value)) {
                return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            }
        } catch (\Exception $e) {
            // Optionally log the error here
        }

        return null;
    }

    /**
     * Insert model to database
     */
    public function model(array $row)
    {
        if (User::where('email', $row['email'])->exists()) {
            return null;
        }

        $user = User::create([
            'name'     => $row['name'],
            'email'    => $row['email'],
            'password' => bcrypt('12345678'),
            'role_id'  => 4,
        ]);

        $tahunMasuk   = now()->format('y');
        $lastStudent  = Student::where('nis', 'like', $tahunMasuk . '%')->orderBy('nis', 'desc')->first();
        $nextNumber   = $lastStudent ? ((int)substr($lastStudent->nis, 2) + 1) : 1;
        $nis          = $tahunMasuk . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return new Student([
            'user_id'           => $user->id,
            'tanggal_bergabung' => $row['tanggal_bergabung'],
            'nis'               => $nis,
            'jenis_kelamin'     => $row['jenis_kelamin'],
            'tanggal_lahir'     => $row['tanggal_lahir'],
        ]);
    }
}
