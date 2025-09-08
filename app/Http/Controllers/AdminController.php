<?php
namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Student;
use App\Models\Assignment;
class AdminController extends Controller
{
    public function dashboard(){
        $teachersCount = Teacher::count();
        $studentsCount = Student::count();
        $assignmentsCount = Assignment::count();
        return view('admindashboard',compact('teachersCount','studentsCount','assignmentsCount'));
    }
}
