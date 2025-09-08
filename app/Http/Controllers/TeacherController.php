<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Account;
use App\Models\Student;
use App\Models\Assignment;
use App\Models\Session;
use App\Models\StudentGoal;
use App\Imports\TeachersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
class TeacherController extends Controller
{
    // Teacher Dashboard
public function dashboard(Request $request)
{
    $account = auth('teacher')->user();

    if (!$account || !$account->teacherProfile) {
        return redirect()->route('teacher.login')->withErrors('Please login as teacher.');
    }

    $teacher = $account->teacherProfile;

    // Load students with assignments and sessions
    $students = Student::with([
        'assignments' => function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        },
        'sessions' => function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        }
    ])->get();

    // Determine which section to show
    $section = $request->query('section', 'assignments'); // default: assignments

    return view('teacherdashboard', compact('teacher', 'students', 'section'));
}
    // ------------------ CRUD ------------------

    // List all teachers (Admin)
    public function index(){
        $teachers = Teacher::with('account')->get();
        return view('teacherindex', compact('teachers'));
    }

    // Show form to create new teacher
  public function create()
{
    $teachersCount = Teacher::count();   // total teachers for sidebar/dashboard
    $studentsCount = Student::count();   // total students for sidebar/dashboard

    return view('teachercreate', compact('teachersCount','studentsCount'));
}


    // Store new teacher
    public function store(Request $request)
    {
        // ✅ Validation
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'email'      => 'required|email|unique:accounts,email',
            'dob'        => 'required|date',
            'phone'      => 'required|digits:10',
            'gender'     => 'required|in:male,female,other',
            'address'    => 'required|string|max:255',
            'city'       => 'required|string|max:100',
            'state'      => 'required|string|max:100',
            'zipcode'    => 'required|digits:6',
            'status'     => 'required|in:active,inactive',
        ]);

        // 1. Create Account
        $account = Account::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'password'   => bcrypt('123456'), // default password
            'role'       => 'teacher',
            'status'     => $validated['status'],
        ]);

        // 2. Create Teacher and link with Account
        Teacher::create([
            'account_id' => $account->id,
            'dob'        => $validated['dob'],
            'phone'      => $validated['phone'],
            'gender'     => $validated['gender'],
            'address'    => $validated['address'],
            'city'       => $validated['city'],
            'state'      => $validated['state'],
            'zipcode'    => $validated['zipcode'],
        ]);

        return redirect()->route('teachers.index')->with('success', 'Teacher created successfully.');
    }

    // Show edit form
    public function edit($id){
        $teacher = Teacher::with('account')->findOrFail($id);
        return view('teacheredit', compact('teacher'));
    }

    // Update teacher
    public function update(Request $request, $id)
    {
        $teacher = Teacher::with('account')->findOrFail($id);

        // ✅ Validation
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

        // Update account fields
        $teacher->account->update([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'status'     => $validated['status'],
        ]);

        // Update teacher fields
        $teacher->update([
            'dob'        => $validated['dob'],
            'phone'      => $validated['phone'],
            'gender'     => $validated['gender'],
            'address'    => $validated['address'],
            'city'       => $validated['city'],
            'state'      => $validated['state'],
            'zipcode'    => $validated['zipcode'],
        ]);

        return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully.');
    }

    // Delete teacher
    public function destroy($id){
        $teacher = Teacher::findOrFail($id);
        $teacher->account->delete(); // also deletes account
        return redirect()->route('teachers.index')->with('success','Teacher deleted successfully');
    }
public function getTeachers(Request $request)
{
    if ($request->ajax()) {
        $data = Teacher::with('account')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('teacher_name', function($row){
                return $row->account 
                    ? $row->account->first_name . ' ' . $row->account->last_name 
                    : 'N/A';
            })
            ->addColumn('email', function($row){
                return $row->account ? $row->account->email : 'N/A';
            })
            ->addColumn('phone', function($row){
                return $row->phone ?? 'N/A';
            })
            ->addColumn('dob', function($row){
                return $row->dob 
                    ? \Carbon\Carbon::parse($row->dob)->format('d-m-Y') 
                    : 'N/A';
            })
            ->addColumn('gender', function($row){
                return ucfirst($row->gender ?? 'N/A');
            })
            ->addColumn('address', function($row){
                return $row->address . ', ' . $row->city . ', ' . $row->state . ' - ' . $row->zipcode;
            })
            ->addColumn('status', function($row){
                return $row->account ? ucfirst($row->account->status) : 'N/A';
            })
            ->addColumn('action', function($row){
                return '
                    <a href="'.route('teachers.edit',$row->id).'" class="btn btn-sm btn-primary">Edit</a>
                    <form action="'.route('teachers.destroy',$row->id).'" method="POST" style="display:inline;">
                        '.csrf_field().method_field("DELETE").'
                        <button type="submit" class="btn btn-sm btn-danger delete-btn">Delete</button>
                    </form>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}

// public function import(Request $request)
// {
//     // Validate the uploaded file
//     $request->validate([
//         'file' => 'required|mimes:csv,txt|max:2048',
//     ]);

//     $file = $request->file('file');
//     $handle = fopen($file, "r");

//     // Read header row
//     $header = fgetcsv($handle, 1000, ",");

//     $rowNumber = 1; // start after header
//     while (($row = fgetcsv($handle, 1000, ",")) !== false) {
//         $rowNumber++;

//         $data = array_combine($header, $row);

//         // Validate each row
//         $validator = Validator::make($data, [
//             'first_name' => 'required|string|max:255',
//             'last_name'  => 'required|string|max:255',
//             'email'      => 'required|email|unique:accounts,email',
//             'password'   => 'required|string|min:6',
//             'status'     => 'required|in:active,inactive',
//             'dob'        => 'required|date',
//             'phone'      => 'required|string|max:15',
//             'gender'     => 'required|in:male,female',
//             'address'    => 'required|string|max:255',
//             'city'       => 'required|string|max:255',
//             'state'      => 'required|string|max:255',
//             'zipcode'    => 'required|string|max:20',
//         ]);

//         if ($validator->fails()) {
//             $errors = $validator->errors()->all();
//             return redirect()->back()->with('error', "Row $rowNumber has errors: " . implode(', ', $errors));
//         }

//         // Create Account
//         $account = Account::create([
//             'first_name' => $data['first_name'],
//             'last_name'  => $data['last_name'],
//             'email'      => $data['email'],
//             'password'   => Hash::make($data['password']),
//             'role'       => 'teacher',
//             'status'     => $data['status'],
//         ]);

//         // Create Teacher linked to Account
//         Teacher::create([
//             'account_id' => $account->id,
//             'dob'        => $data['dob'] ?? null,
//             'phone'      => $data['phone'] ?? null,
//             'gender'     => $data['gender'] ?? null,
//             'address'    => $data['address'] ?? null,
//             'city'       => $data['city'] ?? null,
//             'state'      => $data['state'] ?? null,
//             'zipcode'    => $data['zipcode'] ?? null,
//         ]);
//     }

//     fclose($handle);

//     return redirect("/admin/admindashboard")->with('success', 'Teachers imported successfully!');
// }

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,txt,xlsx,xls|max:2048',
    ]);

    try {
        Excel::import(new TeachersImport, $request->file('file'));
        return back()->with('success', 'Teachers imported successfully!');
    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        $failures = $e->failures();
        $messages = [];
        foreach ($failures as $failure) {
            $messages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
        }
        return back()->with('error', implode('<br>', $messages));
    } catch (\Exception $e) {
        return back()->with('error', 'Import failed: ' . $e->getMessage());
    }
}

public function getAssignmentData(Request $request)
{
    $teacherId = auth()->id(); // get logged in user id

    $assignments = Assignment::with('student')
        ->where('teacher_id', $teacherId)
        ->select('assignments.*');

    return DataTables::of($assignments)
        ->addIndexColumn()
        ->addColumn('student_name', fn($row) => $row->student ? $row->student->first_name.' '.$row->student->last_name : 'N/A')
        ->addColumn('start_date', fn($row) => $row->start_date ? \Carbon\Carbon::parse($row->start_date)->format('d-m-Y') : 'N/A')
        ->addColumn('end_date', fn($row) => $row->end_date ? \Carbon\Carbon::parse($row->end_date)->format('d-m-Y') : 'N/A')
        ->addColumn('weekly_mandate', fn($row) => $row->weekly_mandate ?? 'N/A')
        ->addColumn('daily_mandate', fn($row) => $row->daily_mandate ?? 'N/A')
        ->make(true);
}
public function createSession()
{
    $teacher = auth('teacher')->user()->teacherProfile;

    if (!$teacher) {
        return redirect()->route('teacher.login')->withErrors('Please login as teacher.');
    }

    // Get students assigned to this teacher
    $students = Student::whereHas('assignments', function($q) use ($teacher) {
        $q->where('teacher_id', $teacher->id);
    })->get();

    // Get all student goals
    $allGoals = \App\Models\StudentGoal::select('id','short_term_goal','long_term_goal')->get();

    return view('session.teacher_session', compact('students', 'allGoals'));
}


// Store new session
public function storeSession(Request $request)
{
    $teacher = auth('teacher')->user()->teacherProfile;

    $validated = $request->validate([
        'student_id'    => 'required|exists:students,id',
        'session_date'  => 'required|date',
        'time_in'       => 'required',
        'time_out'      => 'required|after:time_in',
        'goal_ids'      => 'array', // ✅ allow multiple goal checkboxes
    ]);

    // Check overlap (as you already have)
    $overlap = Session::where('student_id', $validated['student_id'])
        ->where('session_date', $validated['session_date'])
        ->where(function($q) use ($validated) {
            $q->whereBetween('time_in', [$validated['time_in'], $validated['time_out']])
              ->orWhereBetween('time_out', [$validated['time_in'], $validated['time_out']])
              ->orWhere(function($q2) use ($validated) {
                  $q2->where('time_in', '<=', $validated['time_in'])
                     ->where('time_out', '>=', $validated['time_out']);
              });
        })
        ->exists();

    if ($overlap) {
        return redirect()->back()->withErrors('This session overlaps with an existing session for the selected student.');
    }

    // ✅ Save session including goal_ids as JSON
    Session::create([
        'teacher_id'   => $teacher->id,
        'student_id'   => $validated['student_id'],
        'session_date' => $validated['session_date'],
        'time_in'      => $validated['time_in'],
        'time_out'     => $validated['time_out'],
        'goal_ids'     => $validated['goal_ids'] ?? [], // store as JSON
    ]);

    return redirect()->route('teacher.dashboard', ['section'=>'sessions'])
                     ->with('success', 'Session added successfully.');
}

public function editSession($id)
{
    $teacher = auth('teacher')->user()->teacherProfile;

    $session = Session::findOrFail($id);

    if ($session->teacher_id !== $teacher->id) {
        return redirect()->route('teacher.dashboard')->withErrors('Unauthorized access.');
    }

    // Get students assigned to this teacher
    $students = Student::whereHas('assignments', function($q) use ($teacher) {
        $q->where('teacher_id', $teacher->id);
    })->get();

    // Load all student goals
    $allGoals = StudentGoal::select('id', 'short_term_goal', 'long_term_goal')->get();

    return view('session.teachereditSession', compact('session', 'students', 'allGoals'));
}


// Update session
public function updateSession(Request $request, $id) {
    $teacher = auth('teacher')->user()->teacherProfile;
    $session = Session::findOrFail($id);

    if ($session->teacher_id !== $teacher->id) {
        return redirect()->route('teacher.dashboard')->withErrors('Unauthorized access.');
    }

    $validated = $request->validate([
        'student_id' => 'required|exists:students,id',
        'session_date' => 'required|date',
        'time_in' => 'required',
        'time_out' => 'required|after:time_in',
    ]);

    $session->update($validated);

    return redirect()->route('teacher.dashboard', ['section'=>'sessions'])->with('success', 'Session updated successfully.');
}

// Delete session
public function deleteSession($id) {
    $teacher = auth('teacher')->user()->teacherProfile;
    $session = Session::findOrFail($id);

    if ($session->teacher_id !== $teacher->id) {
        return redirect()->route('teacher.dashboard')->withErrors('Unauthorized access.');
    }

    $session->delete();

    return redirect()->route('teacher.dashboard', ['section'=>'sessions'])->with('success', 'Session deleted successfully.');
}





}
