<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Teacher;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $teachers = Teacher::all();

        $students = [
            ['first_name'=>'Alice','last_name'=>'Johnson','gender'=>'female','dob'=>'2010-03-12','status'=>'active'],
            ['first_name'=>'Bob','last_name'=>'Williams','gender'=>'male','dob'=>'2011-06-20','status'=>'active'],
            ['first_name'=>'Charlie','last_name'=>'Brown','gender'=>'male','dob'=>'2010-11-05','status'=>'active'],
            ['first_name'=>'Daisy','last_name'=>'Miller','gender'=>'female','dob'=>'2012-01-18','status'=>'active'],
        ];

        foreach($students as $index=>$s){
            $teacher = $teachers[$index % $teachers->count()]; // assign in round-robin

            Student::create([
                'first_name'=>$s['first_name'],
                'last_name'=>$s['last_name'],
                'dob'=>$s['dob'],
                'gender'=>$s['gender'],
                'status'=>$s['status'],
                'address'=>'456 Student Street',
                'city'=>'Townsville',
                'state'=>'State',
                'zipcode'=>'54321',
                'teacher_id'=>$teacher->id
            ]);
        }
    }
}
