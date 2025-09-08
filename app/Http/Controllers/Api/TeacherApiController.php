<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;

class TeacherApiController extends Controller
{
    public function index()
    {
        $account = auth()->user();
        if ($account->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $teachers = Teacher::with('account')->get();
        return response()->json([
            'status' => 'success',
            'teachers' => $teachers
        ]);
    }

    public function store(Request $request)
    {
        $account = auth()->user();

        if ($account->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'email'      => 'required|email|unique:accounts,email',
            'password'   => 'required|min:6',
            'dob'        => 'required|date',
            'phone'      => 'required|digits:10',
            'gender'     => 'required|in:male,female,other',
            'address'    => 'required|string|max:255',
            'city'       => 'required|string|max:100',
            'state'      => 'required|string|max:100',
            'zipcode'    => 'required|digits:6',
            'status'     => 'required|in:active,inactive',
        ]);
        $account = Account::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'role'       => 'teacher',
            'status'     => $validated['status'],
        ]);
        $teacher = Teacher::create([
            'account_id' => $account->id,
            'dob'        => $validated['dob'],
            'phone'      => $validated['phone'],
            'gender'     => $validated['gender'],
            'address'    => $validated['address'],
            'city'       => $validated['city'],
            'state'      => $validated['state'],
            'zipcode'    => $validated['zipcode'],
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Teacher created successfully',
            'teacher' => $teacher
        ], 201);
    }
    public function show($id)
    {
        $account = auth()->user();
        if ($account->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }
        $teacher = Teacher::with('account')->find($id);
        if (!$teacher) {
            return response()->json(['status' => 'error', 'message' => 'Teacher not found'], 404);
        }
        return response()->json(['status' => 'success', 'teacher' => $teacher]);
    }
    public function update(Request $request, $id)
    {
        $account = auth()->user();
        if ($account->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $teacher = Teacher::with('account')->find($id);
        if (!$teacher) {
            return response()->json(['status' => 'error', 'message' => 'Teacher not found'], 404);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'email'      => 'required|email|unique:accounts,email,' . $teacher->account->id,
            'dob'        => 'required|date',
            'phone'      => 'required|digits:10',
            'gender'     => 'required|in:male,female,other',
            'address'    => 'required|string|max:255',
            'city'       => 'required|string|max:100',
            'state'      => 'required|string|max:100',
            'zipcode'    => 'required|digits:6',
            'status'     => 'required|in:active,inactive',
        ]);

        $teacher->account->update([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'status'     => $validated['status'],
        ]);

        $teacher->update([
            'dob'     => $validated['dob'],
            'phone'   => $validated['phone'],
            'gender'  => $validated['gender'],
            'address' => $validated['address'],
            'city'    => $validated['city'],
            'state'   => $validated['state'],
            'zipcode' => $validated['zipcode'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Teacher updated successfully',
            'teacher' => $teacher
        ]);
    }
    public function destroy($id)
    {
        $account = auth()->user();
        if ($account->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $teacher = Teacher::with('account')->find($id);
        if (!$teacher) {
            return response()->json(['status' => 'error', 'message' => 'Teacher not found'], 404);
        }

        $teacher->account->delete();
        $teacher->delete();

        return response()->json(['status' => 'success', 'message' => 'Teacher deleted successfully']);
    }
}
