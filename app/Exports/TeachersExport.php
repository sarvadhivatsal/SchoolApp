<?php

namespace App\Exports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeachersExport implements FromCollection, WithHeadings
{
    /**
     * Export data as collection
     */
    public function collection()
    {
        return Teacher::with('account')->get()->map(function ($teacher) {
            return [
                'first_name' => $teacher->account->first_name ?? '',
                'last_name'  => $teacher->account->last_name ?? '',
                'email'      => $teacher->account->email ?? '',
                'status'     => $teacher->account->status ?? '',
                'dob'        => $teacher->dob,
                'phone'      => $teacher->phone,
                'gender'     => $teacher->gender,
                'address'    => $teacher->address,
                'city'       => $teacher->city,
                'state'      => $teacher->state,
                'zipcode'    => $teacher->zipcode,
            ];
        });
    }

    /**
     * Export file headers
     */
    public function headings(): array
    {
        return [
            'First Name',
            'Last Name',
            'Email',
            'Status',
            'DOB',
            'Phone',
            'Gender',
            'Address',
            'City',
            'State',
            'Zipcode',
        ];
    }
}
