<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Student;
use App\Models\Teacher;

class AdminController extends Controller
{
    public function dashboard()
    {
        $teachersCount = Teacher::count();
        $studentsCount = Student::count();
        $assignmentsCount = Assignment::count();

        return view('admindashboard', compact('teachersCount', 'studentsCount', 'assignmentsCount'));
    }
}
