<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Session;
use App\Models\Student;
use App\Models\StudentGoal;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Session::with(['teacher.account', 'student'])->select('sessions.*');

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
                ->addColumn('teacher_name', fn ($row) => $row->teacher && $row->teacher->account
                        ? $row->teacher->account->first_name.' '.$row->teacher->account->last_name
                        : 'N/A'
                )
                ->addColumn('student_name', fn ($row) => $row->student
                        ? $row->student->first_name.' '.$row->student->last_name
                        : 'N/A'
                )
                ->editColumn('session_date', fn ($row) => Carbon::parse($row->session_date)->format('d M Y'))
                ->editColumn('time_in', fn ($row) => $row->time_in ? Carbon::parse($row->time_in)->format('h:i A') : '')
                ->editColumn('time_out', fn ($row) => $row->time_out ? Carbon::parse($row->time_out)->format('h:i A') : '')
                ->addColumn('action', function ($row) {
                    $edit = '<a href="'.route('admin.sessions.edit', $row->id).'" class="btn btn-sm btn-warning">Edit</a>';
                    $delete = '<form action="'.route('admin.sessions.destroy', $row->id).'" method="POST" style="display:inline;">'
                        .csrf_field().method_field('DELETE')
                        .'<button type="submit" class="btn btn-sm btn-danger delete-btn">Delete</button></form>';

                    return $edit.' '.$delete;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $teachers = Teacher::with('account')->get();
        $students = Student::all();

        return view('session.index', compact('teachers', 'students'));
    }

    public function create()
    {
        $teacherIds = Assignment::pluck('teacher_id')->unique();
        $teachers = Teacher::with('account')->whereIn('id', $teacherIds)->get();

        return view('session.create', compact('teachers'));
    }

    public function store(Request $request)
{
    $request->validate([
        'teacher_id' => 'required|exists:teachers,id',
        'student_id' => 'required|exists:students,id',
        'session_date' => 'required|date',
        'time_in' => 'required|date_format:H:i',
        'time_out' => 'required|date_format:H:i|after:time_in',
        'goal_ids' => 'array',
    ]);

    $timeIn = Carbon::parse($request->time_in);
    $timeOut = Carbon::parse($request->time_out);
    $durationHours = $timeOut->floatDiffInHours($timeIn);

    $teacher = Teacher::findOrFail($request->teacher_id);
    $student = Student::findOrFail($request->student_id);
    $sessionRate = $teacher->hourly_rate * $durationHours;

    // Validate assignment period
    $assignment = Assignment::where('teacher_id', $request->teacher_id)
        ->where('student_id', $request->student_id)
        ->where('start_date', '<=', $request->session_date)
        ->where('end_date', '>=', $request->session_date)
        ->first();

    if (! $assignment) {
        return back()->withErrors(['session_date' => 'Session date must be within assignment period.'])->withInput();
    }

    // Daily mandate checks
    $dailyHoursAssignment = Session::where('teacher_id', $request->teacher_id)
        ->where('student_id', $request->student_id)
        ->whereDate('session_date', $request->session_date)
        ->sum(DB::raw('TIME_TO_SEC(TIMEDIFF(time_out, time_in)) / 3600'));

    $dailyHoursStudent = Session::where('student_id', $request->student_id)
        ->whereDate('session_date', $request->session_date)
        ->sum(DB::raw('TIME_TO_SEC(TIMEDIFF(time_out, time_in)) / 3600'));

    if ($dailyHoursAssignment + $durationHours > $assignment->daily_mandate) {
        return back()->withErrors([
            'time_in' => 'Daily mandate exceeded for this assignment. Max available: '.round($assignment->daily_mandate - $dailyHoursAssignment, 2).' hours.',
        ])->withInput();
    }

    if ($dailyHoursStudent + $durationHours > $student->daily_mandate) {
        return back()->withErrors([
            'time_in' => 'Daily mandate exceeded for this student. Max available: '.round($student->daily_mandate - $dailyHoursStudent, 2).' hours.',
        ])->withInput();
    }

    // Weekly mandate checks
    $weekStart = Carbon::parse($request->session_date)->startOfWeek(Carbon::MONDAY);
    $weekEnd = Carbon::parse($request->session_date)->endOfWeek(Carbon::SUNDAY);

    $weeklyHoursAssignment = Session::where('teacher_id', $request->teacher_id)
        ->where('student_id', $request->student_id)
        ->whereBetween('session_date', [$weekStart, $weekEnd])
        ->sum(DB::raw('TIME_TO_SEC(TIMEDIFF(time_out, time_in)) / 3600'));

    $weeklyHoursStudent = Session::where('student_id', $request->student_id)
        ->whereBetween('session_date', [$weekStart, $weekEnd])
        ->sum(DB::raw('TIME_TO_SEC(TIMEDIFF(time_out, time_in)) / 3600'));

    if ($weeklyHoursAssignment + $durationHours > $assignment->weekly_mandate) {
        return back()->withErrors([
            'time_in' => 'Weekly mandate exceeded for this assignment. Max available: '.round($assignment->weekly_mandate - $weeklyHoursAssignment, 2).' hours.',
        ])->withInput();
    }

    if ($weeklyHoursStudent + $durationHours > $student->weekly_mandate) {
        return back()->withErrors([
            'time_in' => 'Weekly mandate exceeded for this student. Max available: '.round($student->weekly_mandate - $weeklyHoursStudent, 2).' hours.',
        ])->withInput();
    }

    // Create session
    Session::create([
        'teacher_id' => $request->teacher_id,
        'student_id' => $request->student_id,
        'session_date' => $request->session_date,
        'time_in' => $timeIn->format('H:i:s'),
        'time_out' => $timeOut->format('H:i:s'),
        'goal_ids' => $request->goal_ids ?? [],
        'session_rate' => $sessionRate,
    ]);

    return redirect()->route('admin.sessions.index')->with('success', 'Session created successfully.');
}


    public function edit(Session $session)
    {
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
        'time_in' => 'required|date_format:H:i',
        'time_out' => 'required|date_format:H:i|after:time_in',
        'goal_ids' => 'array',
    ]);

    $timeIn = Carbon::parse($request->time_in);
    $timeOut = Carbon::parse($request->time_out);
    $durationHours = $timeOut->floatDiffInHours($timeIn);

    $teacher = $session->teacher; // use existing teacher
    $sessionRate = $teacher->hourly_rate * $durationHours;

    $time_in = $timeIn->format('H:i:s');
    $time_out = $timeOut->format('H:i:s');

    // ✅ Check for overlapping sessions
    $overlap = Session::where('teacher_id', $session->teacher_id)
        ->where('student_id', $session->student_id)
        ->where('id', '!=', $session->id)
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
        return back()->withErrors([
            'time_in' => 'This session overlaps with another session.',
        ])->withInput();
    }

    // ✅ Student DAILY mandate check (exclude current session)
    $dailyHours = Session::where('student_id', $session->student_id)
        ->whereDate('session_date', $request->session_date)
        ->where('id', '!=', $session->id)
        ->sum(DB::raw('TIME_TO_SEC(TIMEDIFF(time_out, time_in)) / 3600'));

    $student = $session->student;
    if ($dailyHours + $durationHours > $student->daily_mandate) {
        return back()->withErrors([
            'time_in' => "Student's daily mandate exceeded. Only ".round($student->daily_mandate - $dailyHours, 2).' hours can be added today.',
        ])->withInput();
    }

    // ✅ Student WEEKLY mandate check (Mon–Sun)
    $weekStart = Carbon::parse($request->session_date)->startOfWeek(Carbon::MONDAY);
    $weekEnd = Carbon::parse($request->session_date)->endOfWeek(Carbon::SUNDAY);

    $weeklyHours = Session::where('student_id', $session->student_id)
        ->whereBetween('session_date', [$weekStart, $weekEnd])
        ->where('id', '!=', $session->id)
        ->sum(DB::raw('TIME_TO_SEC(TIMEDIFF(time_out, time_in)) / 3600'));

    if ($weeklyHours + $durationHours > $student->weekly_mandate) {
        return back()->withErrors([
            'time_in' => "Student's weekly mandate exceeded. Only ".round($student->weekly_mandate - $weeklyHours, 2).' hours can be added this week.',
        ])->withInput();
    }

    // ✅ Update session
    $session->update([
        'session_date' => $request->session_date,
        'time_in' => $time_in,
        'time_out' => $time_out,
        'goal_ids' => $request->goal_ids ?? [],
        'session_rate' => $sessionRate, // use calculated rate
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
        $goals = StudentGoal::all();

        return response()->json($goals);
    }

    public function export(Request $request)
    {
        $query = Session::with(['teacher.account', 'student']);

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

        $fileName = 'sessions.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['ID', 'Teacher', 'Student', 'Date', 'Time In', 'Time Out'];

        $callback = function () use ($sessions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($sessions as $session) {
                fputcsv($file, [
                    $session->id,
                    $session->teacher?->account?->first_name.' '.$session->teacher?->account?->last_name,
                    $session->student?->first_name.' '.$session->student?->last_name,
                    Carbon::parse($session->session_date)->format('d/m/Y'),
                    Carbon::parse($session->time_in)->format('H:i'),
                    Carbon::parse($session->time_out)->format('H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
