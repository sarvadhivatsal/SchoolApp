<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Student::all(['first_name', 'last_name', 'status','parent_email','parent_phone','dob','gender','address','city','state','zipcode','teacher_id']);
    }
}
