<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


    class CoachExport implements FromCollection, WithHeadings
    {
        /**
        * @return \Illuminate\Support\Collection
        */
        public function collection()
        {
            return User::whereIn('role_id', [2, 3])
                ->join('coaches', 'users.id', '=', 'coaches.user_id')
                ->get(['users.name', 'users.email', 'coaches.status', 'coaches.tanggal_bergabung']);
        }

        /**
        * @return array
        */
        public function headings(): array
        {
            return ['Name', 'Email', 'Status', 'Tanggal Bergabung'];
        }
    }
