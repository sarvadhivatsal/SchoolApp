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
            $query = Session::with(['teacher.account', 'student', 'sessionStudents.student'])
                ->select('sessions.*');

            // 🔎 Filter by Teacher
            if ($request->filled('teacher_id')) {
                $query->where('teacher_id', $request->teacher_id);
            }

            // 🔎 Filter by Student (single or multiple)
            if ($request->filled('student_id')) {
                $query->where(function ($q) use ($request) {
                    $q->where('student_id', $request->student_id)
                        ->orWhereHas('sessionStudents', function ($sub) use ($request) {
                            $sub->where('student_ids', $request->student_id);
                        });
                });
            }

            // 🔎 Filter by Date
            if ($request->filled('session_date')) {
                $query->whereDate('session_date', $request->session_date);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('teacher_name', function ($row) {
                    return $row->teacher && $row->teacher->account
                        ? $row->teacher->account->first_name.' '.$row->teacher->account->last_name
                        : 'N/A';
                })
                ->addColumn('student_name', function ($row) {
                    $names = [];

                    // Include single-student session
                    if ($row->student) {
                        $names[] = $row->student->first_name.' '.$row->student->last_name;
                    }

                    // Include all multi-student entries
                    if ($row->sessionStudents && $row->sessionStudents->count()) {
                        foreach ($row->sessionStudents as $ss) {
                            if ($ss->student) {
                                $names[] = $ss->student->first_name.' '.$ss->student->last_name;
                            }
                        }
                    }

                    return $names ? implode(', ', array_unique($names)) : 'N/A';
                })
                ->editColumn('session_date', fn ($row) => Carbon::parse($row->session_date)->format('d M Y')
                )
                ->editColumn('time_in', fn ($row) => $row->time_in ? Carbon::parse($row->time_in)->format('h:i A') : ''
                )
                ->editColumn('time_out', fn ($row) => $row->time_out ? Carbon::parse($row->time_out)->format('h:i A') : ''
                )
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

        // ✅ Pass teachers and students for filters
        $teachers = Teacher::with('account')->get();
        $students = Student::all();

        return view('session.index', compact('teachers', 'students'));
    }

    public function create()
    {
        $teacherIds = Assignment::pluck('teacher_id')->unique();
        $teachers = Teacher::with('account')->whereIn('id', $teacherIds)->get();
        $students = Student::all();

        return view('session.create', compact('teachers', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'student_ids' => 'required|array|min:1',
            'session_date' => 'required|date',
            'time_in' => 'required|date_format:H:i',
            'time_out' => 'required|date_format:H:i|after:time_in',
            'goal_ids' => 'nullable|array',
        ]);

        $timeIn = Carbon::parse($request->time_in);
        $timeOut = Carbon::parse($request->time_out);
        $durationHours = $timeOut->floatDiffInHours($timeIn);

        $teacher = Teacher::findOrFail($request->teacher_id);
        $sessionRate = $teacher->hourly_rate * $durationHours;

        $sessionDate = $request->session_date;

        // ✅ Check that every selected student has a valid assignment on this date
        foreach ($request->student_ids as $studentId) {
            $assignment = Assignment::where('teacher_id', $request->teacher_id)
                ->where('student_id', $studentId)
                ->where('start_date', '<=', $sessionDate)
                ->where('end_date', '>=', $sessionDate)
                ->first();

            if (! $assignment) {
                // Fail early if any student is not valid for the date
                return back()
                    ->withErrors([
                        'session_date' => "Student ID {$studentId} does not have an assignment on {$sessionDate}.",
                    ])
                    ->withInput();
            }
        }

        // ✅ If only one student → store in sessions table
        if (count($request->student_ids) === 1) {
            Session::create([
                'teacher_id' => $request->teacher_id,
                'student_id' => $request->student_ids[0],
                'session_date' => $sessionDate,
                'time_in' => $timeIn->format('H:i:s'),
                'time_out' => $timeOut->format('H:i:s'),
                'goal_ids' => $request->goal_ids ?? [],
                'session_rate' => $sessionRate,
            ]);
        } else {
            // ✅ Multiple students → create base session and link all students
            $session = Session::create([
                'teacher_id' => $request->teacher_id,
                'student_id' => $request->student_ids[0], // FK placeholder
                'session_date' => $sessionDate,
                'time_in' => $timeIn->format('H:i:s'),
                'time_out' => $timeOut->format('H:i:s'),
                'goal_ids' => $request->goal_ids ?? [],
                'session_rate' => $sessionRate,
            ]);

            $rows = [];
            foreach ($request->student_ids as $sid) {
                $rows[] = [
                    'session_id' => $session->id,
                    'student_ids' => $sid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('session_students')->insert($rows);
        }

        return redirect()->route('admin.sessions.index')
            ->with('success', 'Session created successfully.');
    }

    public function edit(Session $session)
    {
        // Load relationships
        $session->load(['teacher.account', 'student', 'sessionStudents.student']);

        // ✅ Get all students assigned to this teacher (via Assignment table)
        $assignedStudentIds = Assignment::where('teacher_id', $session->teacher_id)
            ->pluck('student_id')
            ->unique();

        $allStudents = Student::whereIn('id', $assignedStudentIds)->get();

        // ✅ Collect the students already selected for this session
        $selectedStudents = [];
        if ($session->student_id) {
            $selectedStudents[] = $session->student_id;
        }
        if ($session->sessionStudents && $session->sessionStudents->count()) {
            $selectedStudents = array_merge(
                $selectedStudents,
                $session->sessionStudents->pluck('student_id')->toArray()
            );
        }

        // ✅ Get goals
         $studentIds = $selectedStudents;
        $allGoals = StudentGoal::whereIn('student_id', $studentIds)->get();

        // ✅ Pass everything to the view
        return view('session.edit', compact('session', 'allStudents', 'selectedStudents', 'allGoals'));
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

    public function getGoals($studentId)
    {
        $goals = StudentGoal::where('student_id', $studentId)
            ->select('id', 'short_term_goal', 'long_term_goal')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($goals);
    }

   public function export(Request $request)
{
    $query = Session::with(['teacher.account', 'student', 'sessionStudents.student']);

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

    $columns = ['ID', 'Teacher', 'Students', 'Date', 'Time In', 'Time Out'];

    $callback = function () use ($sessions, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($sessions as $session) {
            // Collect all student names
            $studentNames = [];

            if ($session->student) {
                $studentNames[] = $session->student->first_name . ' ' . $session->student->last_name;
            }

            if ($session->sessionStudents && $session->sessionStudents->count()) {
                foreach ($session->sessionStudents as $s) {
                    $studentNames[] = $s->student->first_name . ' ' . $s->student->last_name;
                }
            }

            $studentsString = implode(', ', $studentNames);

            fputcsv($file, [
                $session->id,
                $session->teacher?->account?->first_name . ' ' . $session->teacher?->account?->last_name,
                $studentsString,
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
