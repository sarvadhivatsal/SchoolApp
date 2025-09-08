<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentGoal;
use App\Models\Student;

class StudentGoalController extends Controller
{
    public function index()
    {
        if (auth('admin')->check()) {
            $goals = StudentGoal::with('student')->get();
        } elseif (auth('teacher')->check()) {
            $teacher = auth('teacher')->user()->teacherProfile;

            $goals = StudentGoal::with('student')
                ->whereHas('student.assignments', function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                })
                ->get();
        } else {
            return redirect()->route('login')->withErrors('Unauthorized access.');
        }

        return view('student_goals.index', compact('goals'));
    }

    public function create()
    {
        if (auth('admin')->check()) {
            $students = Student::all();
        } elseif (auth('teacher')->check()) {
            $teacher = auth('teacher')->user()->teacherProfile;

            $students = Student::whereHas('assignments', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })->get();
        } else {
            return redirect()->route('login')->withErrors('Unauthorized access.');
        }

        return view('student_goals.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'      => 'required|exists:students,id',
            'short_term_goal' => 'required|string|max:255',
            'long_term_goal'  => 'required|string|max:255',
        ]);

        // Teacher check: can only add goals for own students
        if (auth('teacher')->check()) {
            $teacher = auth('teacher')->user()->teacherProfile;

            $ownsStudent = Student::where('id', $request->student_id)
                ->whereHas('assignments', function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                })
                ->exists();

            if (!$ownsStudent) {
                return redirect()->back()->withErrors('You cannot add goals for this student.');
            }
        }

        StudentGoal::create($request->only('student_id', 'short_term_goal', 'long_term_goal'));

        return redirect()->route('student_goals.index')->with('success', 'Goal added successfully.');
    }

    public function edit($id)
    {
        $goal = StudentGoal::with('student')->findOrFail($id);

        if (auth('teacher')->check()) {
            $teacher = auth('teacher')->user()->teacherProfile;

            if (!$goal->student->assignments()->where('teacher_id', $teacher->id)->exists()) {
                return redirect()->route('student_goals.index')->withErrors('Unauthorized access.');
            }
        }

        $students = Student::all();

        return view('student_goals.edit', compact('goal', 'students'));
    }

    public function update(Request $request, $id)
    {
        $goal = StudentGoal::findOrFail($id);

        if (auth('teacher')->check()) {
            $teacher = auth('teacher')->user()->teacherProfile;

            if (!$goal->student->assignments()->where('teacher_id', $teacher->id)->exists()) {
                return redirect()->route('student_goals.index')->withErrors('Unauthorized access.');
            }
        }

        $request->validate([
            'short_term_goal' => 'required|string|max:255',
            'long_term_goal'  => 'required|string|max:255',
        ]);

        $goal->update($request->only('short_term_goal', 'long_term_goal'));

        return redirect()->route('student_goals.index')->with('success', 'Goal updated successfully.');
    }

    public function destroy($id)
    {
        $goal = StudentGoal::findOrFail($id);

        if (auth('teacher')->check()) {
            $teacher = auth('teacher')->user()->teacherProfile;

            if (!$goal->student->assignments()->where('teacher_id', $teacher->id)->exists()) {
                return redirect()->route('student_goals.index')->withErrors('Unauthorized access.');
            }
        }

        $goal->delete();

        return redirect()->route('student_goals.index')->with('success', 'Goal deleted successfully.');
    }
}
