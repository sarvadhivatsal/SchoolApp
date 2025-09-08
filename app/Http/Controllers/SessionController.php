<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\Assignment;
use App\Models\Account;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\StudentGoal;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\SessionsExport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Session::with(['teacher.account', 'student'])->select('sessions.*');

            // ✅ Apply filters
            if ($request->filled('teacher_id')) {
                $query->where('teacher_id', $request->teacher_id);
            }

            if ($request->filled('student_id')) {
                $query->where('student_id', $request->student_id);
            }

            if ($request->filled('session_date')) {
                $query->whereDate('session_date', $request->session_date);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('teacher_name', function ($row) {
                    return $row->teacher && $row->teacher->account
                        ? $row->teacher->account->first_name . ' ' . $row->teacher->account->last_name
                        : 'N/A';
                })
                ->addColumn('student_name', function ($row) {
                    return $row->student
                        ? $row->student->first_name . ' ' . $row->student->last_name
                        : 'N/A';
                })
                ->editColumn('session_date', function ($row) {
                    return \Carbon\Carbon::parse($row->session_date)->format('d M Y');
                })
                ->editColumn('time_in', function ($row) {
                    return $row->time_in ? \Carbon\Carbon::parse($row->time_in)->format('h:i A') : '';
                })
                ->editColumn('time_out', function ($row) {
                    return $row->time_out ? \Carbon\Carbon::parse($row->time_out)->format('h:i A') : '';
                })
                ->addColumn('action', function ($row) {
                    $edit = '<a href="' . route('admin.sessions.edit', $row->id) . '" class="btn btn-sm btn-warning">Edit</a>';
                    $delete = '<form action="' . route('admin.sessions.destroy', $row->id) . '" method="POST" style="display:inline;">'
                        . csrf_field() . method_field("DELETE")
                        . '<button type="submit" class="btn btn-sm btn-danger delete-btn">Delete</button></form>';
                    return $edit . ' ' . $delete;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // Pass filters to blade
        $teachers = Teacher::with('account')->get();
        $students = Student::all();

        return view('session.index', compact('teachers', 'students'));
    }

    public function create()
    {
        // Get teachers that actually have assignments
        $teacherIds = Assignment::pluck('teacher_id')->unique();
        $teachers = Teacher::with('account')->whereIn('id', $teacherIds)->get();

        return view('session.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id'   => 'required|exists:teachers,id',
            'student_id'   => 'required|exists:students,id',
            'session_date' => 'required|date',
            'time_in'      => 'required|date_format:H:i',
            'time_out'     => 'required|date_format:H:i|after:time_in',
            'goal_ids'     => 'array', // multiple checkboxes
        ]);
        //  dd($request->goal_ids);
        $time_in  = Carbon::createFromFormat('H:i', $request->time_in)->format('H:i:s');
        $time_out = Carbon::createFromFormat('H:i', $request->time_out)->format('H:i:s');

        // Ensure the session is within assignment period
        $assignment = Assignment::where('teacher_id', $request->teacher_id)
            ->where('student_id', $request->student_id)
            ->where('start_date', '<=', $request->session_date)
            ->where('end_date', '>=', $request->session_date)
            ->first();

        if (!$assignment) {
            return back()->withErrors(['session_date' => 'Session date must be within assignment period.'])->withInput();
        }

        // Overlap check
        $overlap = Session::where('teacher_id', $request->teacher_id)
            ->where('session_date', $request->session_date)
            ->where(function ($query) use ($time_in, $time_out) {
                $query->whereBetween('time_in', [$time_in, $time_out])
                    ->orWhereBetween('time_out', [$time_in, $time_out])
                    ->orWhere(function ($q) use ($time_in, $time_out) {
                        $q->where('time_in', '<=', $time_in)
                            ->where('time_out', '>=', $time_out);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['time_in' => 'This session overlaps with another session.'])->withInput();
        }

        // ✅ Create session and store goal_ids in sessions table
        Session::create([
            'teacher_id'   => $request->teacher_id,
            'student_id'   => $request->student_id,
            'session_date' => $request->session_date,
            'time_in'      => $time_in,
            'time_out'     => $time_out,
            'goal_ids'     => $request->goal_ids ?? [], // stored as JSON
        ]);

        return redirect()->route('admin.sessions.index')->with('success', 'Session created successfully.');
    }


    public function edit(Session $session)
    {
        // Load teacher + student with relationships
        $session->load(['teacher.account', 'student']);

        $allGoals = StudentGoal::select('id', 'short_term_goal', 'long_term_goal')
            ->orderBy('id')
            ->get();

        return view('session.edit', compact('session', 'allGoals'));
    }


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

        // Validate assignment period
        $assignment = Assignment::where('teacher_id', $session->teacher_id)
            ->where('student_id', $session->student_id)
            ->where('start_date', '<=', $request->session_date)
            ->where('end_date', '>=', $request->session_date)
            ->first();

        if (!$assignment) {
            return back()->withErrors([
                'session_date' => 'Session date must be within assignment period.'
            ])->withInput();
        }

        // Overlap check
        $overlap = Session::where('teacher_id', $session->teacher_id)
            ->where('session_date', $request->session_date)
            ->where('id', '!=', $session->id)
            ->where(function ($query) use ($time_in, $time_out) {
                $query->whereBetween('time_in', [$time_in, $time_out])
                    ->orWhereBetween('time_out', [$time_in, $time_out])
                    ->orWhere(function ($q) use ($time_in, $time_out) {
                        $q->where('time_in', '<=', $time_in)
                            ->where('time_out', '>=', $time_out);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors([
                'time_in' => 'This session overlaps with another session.'
            ])->withInput();
        }

        // ✅ Update session including goal_ids
        $session->update([
            'session_date' => $request->session_date,
            'time_in'      => $time_in,
            'time_out'     => $time_out,
            'goal_ids'     => $request->goal_ids ?? [],
        ]);

        return redirect()->route('admin.sessions.index')
            ->with('success', 'Session updated successfully.');
    }



    public function destroy(Session $session)
    {
        $session->delete();
        return redirect()->route('admin.sessions.index')->with('success', 'Session deleted successfully.');
    }

    public function getStudents($teacherId)
    {
        $studentIds = Assignment::where('teacher_id', $teacherId)->pluck('student_id')->unique();
        $students = Student::whereIn('id', $studentIds)->get(['id', 'first_name', 'last_name']);
        return response()->json($students);
    }

    public function getGoals()
    {
        $goals = \App\Models\StudentGoal::all();
        return response()->json($goals);
    }
    // public function teacherSession()
    // {
    //     $teacherId = auth('teacher')->id();

    //     // Get students assigned to this teacher
    //     $studentIds = Assignment::where('teacher_id', $teacherId)
    //                             ->pluck('student_id')
    //                             ->unique();

    //     $students = Student::whereIn('id', $studentIds)
    //                        ->select('id','first_name','last_name')
    //                        ->get(); // ✅ This is a Collection

    //     // Get all goals
    //     $allGoals = StudentGoal::select('id','short_term_goal','long_term_goal')->get(); // ✅ Collection

    //     // Pass as array
    //     return view('session.teacher_session', [
    //         'students' => $students,
    //         'allGoals' => $allGoals,
    //     ]);

    public function export(Request $request)
    {
        $query = Session::with(['teacher.account', 'student']);

        // Apply filters
        if ($request->teacher_id) {
            $query->where('teacher_id', $request->teacher_id);
        }
        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->session_date) {
            $query->whereDate('session_date', $request->session_date);
        }

        $sessions = $query->get();

        // Generate CSV
        $fileName = 'sessions.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID', 'Teacher', 'Student', 'Date', 'Time In', 'Time Out'];

        $callback = function () use ($sessions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($sessions as $session) {
                fputcsv($file, [
                    $session->id,
                    $session->teacher?->account?->first_name . ' ' . $session->teacher?->account?->last_name,
                    $session->student?->first_name . ' ' . $session->student?->last_name,
                    \Carbon\Carbon::parse($session->session_date)->format('d/m/Y'),
                    \Carbon\Carbon::parse($session->time_in)->format('H:i'),
                    \Carbon\Carbon::parse($session->time_out)->format('H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
