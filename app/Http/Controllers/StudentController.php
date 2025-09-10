<?php

namespace App\Http\Controllers;

use App\Imports\StudentsImport;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class StudentController extends Controller
{
    // List all students (for admin)
    public function index()
    {
        $students = Student::with('teacher.account')->get();

        return view('studentindex', compact('students'));
    }

    // Show create form
    public function create()
    {
        $teachers = Teacher::with('account')->get();

        return view('studentcreate', compact('teachers'));
    }

    // Store new student
    public function store(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'teacher_id'     => 'required|exists:teachers,id',
            'dob'            => 'required|date',
            'parent_phone'   => 'required|digits:10',
            'gender'         => 'required|in:male,female,other',
            'address'        => 'required|string|max:255',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'zipcode'        => ['required', 'regex:/^\d{6}$/'],
            'status'         => 'required|in:active,inactive',
            'daily_mandate'  => 'required|numeric|min:0',
            'weekly_mandate' => 'required|numeric|min:0',
        ]);

        Student::create($request->all());

        return redirect()->route('students.index')
            ->with('success', 'Student added successfully.');
    }

    // Show edit form
    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $teachers = Teacher::with('account')->get();

        return view('studentedit', compact('student', 'teachers'));
    }

    // Update student
    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'teacher_id'     => 'required|exists:teachers,id',
            'dob'            => 'required|date',
            'parent_phone'   => 'required|digits:10',
            'gender'         => 'required|in:male,female,other',
            'address'        => 'required|string|max:255',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'zipcode'        => ['required', 'regex:/^\d{6}$/'],
            'status'         => 'required|in:active,inactive',
            'daily_mandate'  => 'required|numeric|min:0',
            'weekly_mandate' => 'required|numeric|min:0',
        ]);

        $student = Student::findOrFail($id);
        $student->update($request->all());

       $account = auth()->user(); // ✅ this is Account model

        if ($account && $account->role === 'admin') {
            return redirect()->route('admin.dashboard')
                             ->with('success', 'Student updated successfully');
        } elseif ($account && $account->role === 'teacher') {
            return redirect()->route('teacher.dashboard')
                             ->with('success', 'Student updated successfully');
        }

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully');
    }

    // Delete student
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        $user = auth()->user();

        if ($user && $user->role == 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Student deleted successfully');
        } elseif ($user && $user->role == 'teacher') {
            return redirect()->route('teacher.dashboard')
                ->with('success', 'Student deleted successfully');
        }

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully');
    }

    // Datatables
    public function getStudents(Request $request)
    {
        if ($request->ajax()) {
            $data = Student::with(['teacher.account'])->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('full_name', fn($row) => $row->first_name.' '.$row->last_name)
                ->addColumn('email', fn($row) => $row->parent_email ?? 'N/A')
                ->addColumn('phone', fn($row) => $row->parent_phone ?? 'N/A')
                ->addColumn('teacher_name', function ($row) {
                    return $row->teacher && $row->teacher->account
                        ? $row->teacher->account->first_name.' '.$row->teacher->account->last_name
                        : 'N/A';
                })
                ->addColumn('status', fn($row) => ucfirst($row->status))
                ->addColumn('daily_mandate', fn($row) => $row->daily_mandate ?? '0')
                ->addColumn('weekly_mandate', fn($row) => $row->weekly_mandate ?? '0')
                ->addColumn('action', function ($row) {
                    return '
                        <a href="'.route('students.edit', $row->id).'" class="btn btn-sm btn-primary">Edit</a>
                        <form action="'.route('students.destroy', $row->id).'" method="POST" style="display:inline;">
                            '.csrf_field().method_field('DELETE').'
                            <button type="submit" class="btn btn-sm btn-danger delete-btn">Delete</button>
                        </form>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    // Import students
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls|max:2048',
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file'));

            return back()->with('success', 'Students imported successfully!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $messages = [];
            foreach ($failures as $failure) {
                $messages[] = "Row {$failure->row()}: ".implode(', ', $failure->errors());
            }

            return back()->with('error', implode('<br>', $messages));
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: '.$e->getMessage());
        }
    }
}
