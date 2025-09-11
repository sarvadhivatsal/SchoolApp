@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Payroll</h1>

    {{-- Refresh Button --}}
    <form action="{{ route('admin.payroll.refresh') }}" method="POST" class="mb-3">
        @csrf
        <button type="submit" class="btn btn-primary">REFRESH RATE</button>
    </form>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Payroll Table --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Teacher</th>
                <th>Student</th>
                <th>Month</th>
                <th>Year</th>
                <th>Total Session Duration</th>
                <th>Session Rate</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $index => $session)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $session->teacher_name }}</td>
                    <td>{{ $session->student_name }}</td>
                    <td>{{ date('F', mktime(0, 0, 0, $session->month, 1)) }}</td>
                    <td>{{ $session->year }}</td>
                    {{-- ✅ Use session_duration instead of total_duration --}}
                    <td>{{ number_format($session->session_duration, 2) }}</td>
                    <td>{{ $session->session_rate }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No payroll data found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
