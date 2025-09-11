<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Payroll;

class PayrollController extends Controller
{
    /**
     * Display the payroll list.
     */
    
   public function index()
{
    $sessions = DB::table('payroll as p')
        ->join('teachers as t','p.teacher_id','=','t.id')
        ->join('accounts as a','t.account_id','=','a.id') // teacher name from accounts
        ->join('students as st','p.student_id','=','st.id') // student name from students
        ->select(
            'p.teacher_id',
            'p.student_id',
            'p.month',
            'p.year',
            'p.session_duration',
            'p.session_rate',
            DB::raw("CONCAT(a.first_name,' ',a.last_name) as teacher_name"),
            DB::raw("CONCAT(st.first_name,' ',st.last_name) as student_name")
        )
        ->orderBy('p.year','desc')
        ->orderBy('p.month','desc')
        ->get();

    return view('payroll.index', compact('sessions'));
}


    /**
     * Refresh payroll and store records into payroll table.
     */
  public function refresh()
{
    // Aggregate sessions by teacher, student, month/year
    $sessions = DB::table('sessions as s')
        ->select(
            's.teacher_id',
            's.student_id',
            DB::raw('MONTH(s.session_date) as month'),
            DB::raw('YEAR(s.session_date) as year'),
            DB::raw('SUM(TIMESTAMPDIFF(MINUTE, s.time_in, s.time_out)/60) as session_duration'),
            DB::raw('MAX(s.session_rate) as session_rate')
        )
        ->groupBy('s.teacher_id','s.student_id',DB::raw('YEAR(s.session_date)'),DB::raw('MONTH(s.session_date)'))
        ->get();

    foreach ($sessions as $session) {
        $amount = $session->session_duration * $session->session_rate;

        // Save only IDs to payroll table
        DB::table('payroll')->updateOrInsert(
            [
                'teacher_id' => $session->teacher_id,
                'student_id' => $session->student_id,
                'month'      => $session->month,
                'year'       => $session->year,
            ],
            [
                'session_duration' => $session->session_duration,
                'session_rate'     => $session->session_rate,
                // 'amount'           => $amount,
                'updated_at'       => now(),
                'created_at'       => now(),
            ]
        );
    }

    return redirect()->route('admin.payroll.index')
        ->with('success', 'Payroll refreshed successfully!');
}

}
