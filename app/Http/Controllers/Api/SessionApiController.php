<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SessionApiController extends Controller
{
   public function index(Request $request)
{
    /** @var \App\Models\Account $account */
    $account = Auth::guard('sanctum')->user(); // Authenticated Account
    //  dd($account);
    $query = Session::with(['teacher.account', 'student']);

    // If teacher, only show their sessions
    if ($account->isTeacher()) {
        $teacherProfile = $account->teacherProfile; // one-to-one relationship
        if ($teacherProfile) {
            $query->where('teacher_id', $teacherProfile->id);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No teacher profile found for this account.'
            ], 404);
        }
    }

    // Admin sees all sessions automatically

    $sessions = $query->get();

    return response()->json([
        'status' => 'success',
        'sessions' => $sessions
    ]);
}

    // Create session
    public function store(Request $request)
    {
        $request->validate([
            'teacher_id'   => 'required|exists:accounts,id',
            'student_id'   => 'required|exists:students,id',
            'session_date' => 'required|date',
            'time_in'      => 'required|date_format:H:i',
            'time_out'     => 'required|date_format:H:i|after:time_in',
            'goal_ids'     => 'array',
        ]);
//  dd($request->all());

        $time_in  = Carbon::createFromFormat('H:i', $request->time_in)->format('H:i:s');
        $time_out = Carbon::createFromFormat('H:i', $request->time_out)->format('H:i:s');

        // Check assignment
        $assignment = Assignment::where('teacher_id', $request->teacher_id)
            ->where('student_id', $request->student_id)
            ->where('start_date', '<=', $request->session_date)
            ->where('end_date', '>=', $request->session_date)
            ->first();

        if (!$assignment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session date must be within assignment period.'
            ], 422);
        }

        // Check overlap
        $overlap = Session::where('teacher_id', $request->teacher_id)
            ->where('session_date', $request->session_date)
            ->where(function($q) use ($time_in, $time_out) {
                $q->whereBetween('time_in', [$time_in, $time_out])
                  ->orWhereBetween('time_out', [$time_in, $time_out])
                  ->orWhere(function($qq) use ($time_in, $time_out) {
                      $qq->where('time_in', '<=', $time_in)
                         ->where('time_out', '>=', $time_out);
                  });
            })
            ->exists();

        if ($overlap) {
            return response()->json([
                'status' => 'error',
                'message' => 'This session overlaps with another session.'
            ], 422);
        }

        // Create session
        $session = Session::create([
            'teacher_id'   => $request->teacher_id,
            'student_id'   => $request->student_id,
            'session_date' => $request->session_date,
            'time_in'      => $time_in,
            'time_out'     => $time_out,
            'goal_ids'     => $request->goal_ids ?? [],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Session created successfully.',
            'session' => $session
        ], 201);
    }

    // Show a single session
    public function show(Session $session)
    {
        $session->load(['teacher.account', 'student']);
        return response()->json([
            'status' => 'success',
            'session' => $session
        ]);
    }

    // Update a session
    public function update(Request $request, Session $session)
    {
        $request->validate([
            'session_date' => 'required|date',
            'time_in'      => 'required|date_format:H:i',
            'time_out'     => 'required|date_format:H:i|after:time_in',
            'goal_ids'     => 'array',
        ]);

        $time_in  = Carbon::createFromFormat('H:i', $request->time_in)->format('H:i:s');
        $time_out = Carbon::createFromFormat('H:i', $request->time_out)->format('H:i:s');

        // Check assignment
        $assignment = Assignment::where('teacher_id', $session->teacher_id)
            ->where('student_id', $session->student_id)
            ->where('start_date', '<=', $request->session_date)
            ->where('end_date', '>=', $request->session_date)
            ->first();

        if (!$assignment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session date must be within assignment period.'
            ], 422);
        }

        // Check overlap
        $overlap = Session::where('teacher_id', $session->teacher_id)
            ->where('session_date', $request->session_date)
            ->where('id', '!=', $session->id)
            ->where(function($q) use ($time_in, $time_out) {
                $q->whereBetween('time_in', [$time_in, $time_out])
                  ->orWhereBetween('time_out', [$time_in, $time_out])
                  ->orWhere(function($qq) use ($time_in, $time_out) {
                      $qq->where('time_in', '<=', $time_in)
                         ->where('time_out', '>=', $time_out);
                  });
            })
            ->exists();

        if ($overlap) {
            return response()->json([
                'status' => 'error',
                'message' => 'This session overlaps with another session.'
            ], 422);
        }

        $session->update([
            'session_date' => $request->session_date,
            'time_in'      => $time_in,
            'time_out'     => $time_out,
            'goal_ids'     => $request->goal_ids ?? [],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Session updated successfully.',
            'session' => $session
        ]);
    }

    // Delete a session
    public function destroy(Session $session)
    {
        $session->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Session deleted successfully.'
        ]);
    }
}
