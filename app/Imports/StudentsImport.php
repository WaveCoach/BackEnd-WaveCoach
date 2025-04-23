<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use App\Models\Package;
use App\Models\PackageStudent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Collection;

class StudentsImport implements ToCollection, WithHeadingRow
{
    /**
     * Process the collection of rows.
     */
    public function collection(Collection $rows)
{
    // Dump seluruh collection (semua rows) untuk debugging

    foreach ($rows as $row) {
        try {
            // Cek jika email sudah ada
            if (User::where('email', $row['email'] ?? null)->exists()) {
                Log::info("User dengan email {$row['email']} sudah ada.");
                continue;
            }

            // Buat User baru
            $user = User::create([
                'name'     => $row['name'] ?? null,
                'email'    => $row['email'] ?? null,
                'password' => bcrypt('12345678'),
                'role_id'  => 4,
            ]);

            // Proses tanggal bergabung
            $tanggal_bergabung = $this->parseTanggal($row['tanggal_bergabung'] ?? null);
            $tanggal_lahir = $this->parseTanggal($row['tanggal_lahir'] ?? null);

            // Buat data Student
            $tahunMasuk = now()->format('y');
            $lastStudent = Student::where('nis', 'like', $tahunMasuk . '%')->orderBy('nis', 'desc')->first();
            $nextNumber = $lastStudent ? ((int)substr($lastStudent->nis, 2) + 1) : 1;
            $nis = $tahunMasuk . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            $student = new Student([
                'user_id' => $user->id,
                'tanggal_bergabung' => $tanggal_bergabung,
                'nis' => $nis,
                'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
                'tanggal_lahir' => $tanggal_lahir,
            ]);
            $student->save();

            // Proses Package
            if (!empty($row['package'])) {
                // Ambil semua Package dan index berdasarkan nama
                $allPackages = Package::all()->keyBy(fn($item) => strtolower(trim($item->name)));
                $packageNames = array_map('trim', explode(',', $row['package']));

                foreach ($packageNames as $packageName) {
                    // Normalisasi nama paket
                    $normalized = strtolower(trim($packageName));
                    Log::info("Mencari package dengan nama '{$normalized}'");

                    if ($allPackages->has($normalized)) {
                        $package = $allPackages->get($normalized);
                        PackageStudent::create([
                            'student_id'   => $user->id,
                            'package_id' => $package->id,
                        ]);
                        Log::info("Package '{$normalized}' berhasil ditambahkan untuk coach {$user->id}");
                    } else {
                        Log::warning("Package dengan nama '{$normalized}' tidak ditemukan.");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Gagal mengimport data student: {$e->getMessage()}");
            continue;
        }
    }
}


    /**
     * Convert Excel date format or string to Y-m-d.
     */
    private function parseTanggal($value)
    {
        try {
            Log::info("Parsing tanggal: " . $value);

            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            } elseif (is_string($value)) {
                return Carbon::parse($value)->format('Y-m-d');
            }
        } catch (\Exception $e) {
            Log::warning("Gagal parsing tanggal: " . $value);
        }

        return null;
    }
}
