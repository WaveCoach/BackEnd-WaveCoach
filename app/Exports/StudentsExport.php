<?php

namespace App\Exports;

use App\Models\Students;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::where('role_id', 4)
        ->join('students', 'users.id', '=', 'students.user_id')
        ->get(['users.name', 'users.email', 'students.tanggal_bergabung', 'students.tanggal_bergabung', 'nis']);
    }
}
