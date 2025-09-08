<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // \Log::info('Row imported:', $row); // ✅ Debug: check storage/logs/laravel.log

        return new Student([
            'first_name'   => $row['first_name'],
            'last_name'    => $row['last_name'],
            'status'       => $row['status'],
            'parent_email' => $row['parent_email'],
            'parent_phone' => $row['parent_phone'],
            'dob'          => isset($row['dob']) ? \Carbon\Carbon::parse($row['dob'])->format('Y-m-d') : null,
            'gender'       => $row['gender'],
            'address'      => $row['address'],
            'city'         => $row['city'],
            'state'        => $row['state'],
            'zipcode'      => $row['zipcode'],
            'teacher_id'   => $row['teacher_id'] ?? null, // ✅ allow NULL teacher
        ]);
    }
}
