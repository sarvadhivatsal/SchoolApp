<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AssignmentController extends Controller
{
    // Show assignments list
    public function index()
    {
        return view('assignment.index');
    }

    // Show create form
    public function create()
    {
        $teachers = Teacher::with('account')->get();
        $students = Student::all();

        return view('assignment.create', compact('teachers', 'students'));
    }

    // Store a new assignment
    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'student_id' => 'required|exists:students,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'weekly_mandate' => 'required|numeric|min:0.1',
            'daily_mandate' => 'required|integer|min:1',
        ]);

        Assignment::create($request->all());

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment created successfully');
    }

    // Edit assignment (for edit page or modal)
    public function edit($id)
    {
        $assignment = Assignment::findOrFail($id);
        $teachers = Teacher::with('account')->get();
        $students = Student::all();

        return view('assignment.edit', compact('assignment', 'teachers', 'students'));
    }

    // Update assignment
    public function update(Request $request, $id)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'student_id' => 'required|exists:students,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'weekly_mandate' => 'required|numeric|min:0.1',
            'daily_mandate' => 'required|integer|min:1',
        ]);

        $assignment = Assignment::findOrFail($id);
        $assignment->update($request->all());

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment updated successfully');
    }

    // Delete assignment
    public function destroy($id)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->delete();

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment deleted successfully');
    }

    // DataTables AJAX
    public function getData()
    {
        $assignments = Assignment::with(['teacher.account', 'student'])->select('assignments.*');

        return datatables()->of($assignments)
            ->addColumn('teacher_name', function ($row) {
                return $row->teacher->account->first_name.' '.$row->teacher->account->last_name;
            })
            ->addColumn('student_name', function ($row) {
                return $row->student->first_name.' '.$row->student->last_name;
            })
            // 🔎 Search fix
            ->filterColumn('teacher_name', function ($query, $keyword) {
                $query->whereHas('teacher.account', function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('student_name', function ($query, $keyword) {
                $query->whereHas('student', function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%");
                });
            })
            // ⬆️ Sorting fix
            ->orderColumn('teacher_name', function ($query, $order) {
                $query->join('teachers', 'teachers.id', '=', 'assignments.teacher_id')
                    ->join('accounts', 'accounts.id', '=', 'teachers.account_id')
                    ->orderByRaw("CONCAT(accounts.first_name, ' ', accounts.last_name) {$order}");
            })
            ->orderColumn('student_name', function ($query, $order) {
                $query->join('students', 'students.id', '=', 'assignments.student_id')
                    ->orderByRaw("CONCAT(students.first_name, ' ', students.last_name) {$order}");
            })
            ->addColumn('action', function ($row) {
                return '
                <a href="'.route('admin.assignments.edit', $row->id).'" class="btn btn-sm btn-warning">Edit</a>
                <form action="'.route('admin.assignments.destroy', $row->id).'" method="POST" style="display:inline-block;">
                    '.csrf_field().method_field('DELETE').'
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
            ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
