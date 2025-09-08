<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentGoal;
use App\Models\Student;
class StudentGoalApiController extends Controller
{
    public function index()
    {
        $goals = StudentGoal::with('student')->get();
        return response()->json([
            'status' => 'success',
            'goals' => $goals
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'      => 'required|exists:students,id',
            'short_term_goal' => 'required|string|max:255',
            'long_term_goal'  => 'required|string|max:255',
        ]);

        $goal = StudentGoal::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Goal created successfully',
            'goal' => $goal
        ], 201);
    }

     public function show($id)
    {
        $goal = StudentGoal::with('student')->find($id);

        if (!$goal) {
            return response()->json([
                'status' => 'error',
                'message' => 'Goal not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'goal' => $goal
        ]);
    }

     public function update(Request $request, $id)
    {
        $goal = StudentGoal::find($id);

        if (!$goal) {
            return response()->json([
                'status' => 'error',
                'message' => 'Goal not found'
            ], 404);
        }

        $validated = $request->validate([
            'short_term_goal' => 'required|string|max:255',
            'long_term_goal'  => 'required|string|max:255',
        ]);

        $goal->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Goal updated successfully',
            'goal' => $goal
        ]);
    }


    public function destroy($id)
    {
        $goal = StudentGoal::find($id);

        if (!$goal) {
            return response()->json([
                'status' => 'error',
                'message' => 'Goal not found'
            ], 404);
        }

        $goal->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Goal deleted successfully'
        ]);
    }
}
