<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class StudentApiController extends Controller
{
    public function index()
    {
        $account = auth()->user();
        if ($account->role !== 'admin') {
            return response()->json(['status'=>'error','message'=>'Unauthorized'],403);
        }

        $students = Student::with('teacher.account')->get();
        return response()->json([
            'status' => 'success',
            'students' => $students
        ]);
    }


    public function store(Request $request)
    {
          $account = auth()->user();
        if ($account->role !== 'admin') {
            return response()->json(['status'=>'error','message'=>'Unauthorized'],403);
        }

        $validated = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'teacher_id'   => 'required|exists:teachers,id',
            'dob'          => 'required|date',
            'parent_email' => 'nullable|email',
            'parent_phone' => 'required|digits:10',
            'gender'       => 'required|in:male,female,other',
            'address'      => 'required|string|max:255',
            'city'         => 'required|string|max:100',
            'state'        => 'required|string|max:100',
            'zipcode'      => ['required','regex:/^\d{6}$/'],
            'status'       => 'required|in:active,inactive',
        ]);

        $student = Student::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Student created successfully',
            'student' => $student
        ], 201);
    }

    public function show($id)
    {
        $account = auth()->user();
        if ($account->role !== 'admin') {
            return response()->json(['status'=>'error','message'=>'Unauthorized'],403);
        }

        $student = Student::with('teacher.account')->find($id);
        if (!$student) {
            return response()->json(['status'=>'error','message'=>'Student not found'],404);
        }
        return response()->json(['status'=>'success','student'=>$student]);
    }

    public function update(Request $request,$id)
    {
         $account = auth()->user();
        if ($account->role !== 'admin') {
            return response()->json(['status'=>'error','message'=>'Unauthorized'],403);
        }

        $student = Student::find($id);
        if (!$student) {
            return response()->json(['status'=>'error','message'=>'Student not found'],404);
        }

        $validated = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'teacher_id'   => 'required|exists:teachers,id',
            'dob'          => 'required|date',
            'parent_email' => 'nullable|email',
            'parent_phone' => 'required|digits:10',
            'gender'       => 'required|in:male,female,other',
            'address'      => 'required|string|max:255',
            'city'         => 'required|string|max:100',
            'state'        => 'required|string|max:100',
            'zipcode'      => ['required','regex:/^\d{6}$/'],
            'status'       => 'required|in:active,inactive',
        ]);

        $student->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Student updated successfully',
            'student' => $student
        ]);
    }
    public function destroy($id)
    {
        $account = auth()->user();
        if ($account->role !== 'admin') {
            return response()->json(['status'=>'error','message'=>'Unauthorized'],403);
        }

        $student = Student::find($id);
        if (!$student) {
            return response()->json(['status'=>'error','message'=>'Student not found'],404);
        }

        $student->delete();

        return response()->json(['status'=>'success','message'=>'Student deleted successfully']);
    }
}
