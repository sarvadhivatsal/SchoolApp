<?php

namespace App\Exports;

use App\Models\Session;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
class SessionsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Session::with(['teacher.account', 'student'])->get();
    }
    public function map($session): array
    {
        return [
            $session->id,
            $session->teacher?->account?->first_name . ' ' . $session->teacher?->account?->last_name,
            $session->student?->first_name . ' ' . $session->student?->last_name,
            $session->session_date,
            $session->time_in,
            $session->time_out,
        ];
    }
     public function headings(): array
    {
        return [
            'ID',
            'Teacher',
            'Student',
            'Date',
            'Time In',
            'Time Out',
        ];
    }
}
