<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\Payroll;
use Yajra\DataTables\Facades\DataTables;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $sessions = Session::with(['teacher', 'student'])->select('sessions.*');

            return DataTables::of($sessions)
                ->addIndexColumn()
                ->addColumn('teacher_name', fn($session) => $session->teacher?->name ?? 'N/A')
                ->addColumn('student_name', fn($session) => $session->student?->name ?? 'N/A')
                ->addColumn('month', fn($session) => $session->created_at?->format('F') ?? '-')
                ->addColumn('year', fn($session) => $session->created_at?->format('Y') ?? '-')
                ->addColumn('session_duration', fn($session) => $session->total_hours)
                ->addColumn('session_rate', fn($session) => $session->rate)
                ->addColumn('session_id', fn($session) => $session->id)
                ->rawColumns(['session_id'])
                ->make(true);
        }

        return view('payroll.index');
    }

    public function refreshRate($sessionId)
    {
        $session = Session::with(['teacher', 'student'])->findOrFail($sessionId);

        $totalAmount = $session->total_hours * $session->rate;

        Payroll::updateOrCreate(
            ['session_id' => $session->id],
            [
                'teacher_id' => $session->teacher_id,
                'student_id' => $session->student_id,
                'total_hours' => $session->total_hours,
                'session_rate' => $session->rate,
                'amount' => $totalAmount
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Payroll refreshed for session ID {$session->id}"
        ]);
    }
}
