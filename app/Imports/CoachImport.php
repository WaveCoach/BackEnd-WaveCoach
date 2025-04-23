<?php

namespace App\Imports;

use App\Models\Coaches;
use App\Models\Package;
use App\Models\PackageCoach;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CoachImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {

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
                    'role_id'  => 2,
                ]);

                // Proses tanggal bergabung
                $tanggal = null;
                if (!empty($row['tanggal_bergabung'])) {
                    if (is_numeric($row['tanggal_bergabung'])) {
                        $tanggal = Carbon::instance(Date::excelToDateTimeObject($row['tanggal_bergabung']))->format('Y-m-d');
                    } else {
                        $tanggal = Carbon::createFromFormat('d/m/Y', $row['tanggal_bergabung'])->format('Y-m-d');
                    }
                }

                // Buat data Coach
                Coaches::create([
                    'user_id'           => $user->id,
                    'tanggal_bergabung' => $tanggal,
                ]);

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
                            PackageCoach::create([
                                'coach_id'   => $user->id,
                                'package_id' => $package->id,
                            ]);
                            Log::info("Package '{$normalized}' berhasil ditambahkan untuk coach {$user->id}");
                        } else {
                            Log::warning("Package dengan nama '{$normalized}' tidak ditemukan.");
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Gagal mengimport data coach: {$e->getMessage()}");
                continue;
            }
        }
    }
}
