@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="container-fluid">
    <h3>Welcome, {{ $teacher->account->first_name }} {{ $teacher->account->last_name }}</h3>

    <div class="mb-3">
        @if($section === 'sessions')
            <a href="{{ route('teacher.createSession') }}" class="btn btn-success">Add New Session</a>
        @endif
    </div>

    {{-- Assignments Table --}}
    @if($section === 'assignments')
        <div class="card shadow-sm mt-2">
            <div class="card-header bg-primary text-white fw-bold">Your Assignments</div>
            <div class="card-body">
                @if($students->pluck('assignments')->flatten()->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    {{-- <th>Title</th> --}}
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Weekly Mandate</th>
                                    <th>Daily Mandate</th>
                                    {{-- <th>Actions</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @php $counter = 1; @endphp
                                @foreach($students as $student)
                                    @foreach($student->assignments as $assignment)
                                        <tr>
                                            <td>{{ $counter++ }}</td>
                                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                            {{-- <td>{{ $assignment->title ?? 'N/A' }}</td> --}}
                                            <td>{{ $assignment->start_date ? \Carbon\Carbon::parse($assignment->start_date)->format('d-m-Y') : 'N/A' }}</td>
                                            <td>{{ $assignment->end_date ? \Carbon\Carbon::parse($assignment->end_date)->format('d-m-Y') : 'N/A' }}</td>
                                            <td>{{ $assignment->weekly_mandate ?? '-' }}</td>
                                            <td>{{ $assignment->daily_mandate ?? '-' }}</td>
                                            {{-- <td>
                                                <a href="{{ route('teacher.editAssignment', $assignment->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form action="{{ route('teacher.deleteAssignment', $assignment->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger delete-btn">Delete</button>
                                                </form>
                                            </td> --}}
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No assignments found.</p>
                @endif
            </div>
        </div>
    @endif

    {{-- Sessions Table --}}
    {{-- Sessions Table --}}
@if($section === 'sessions')
    <div class="card shadow-sm mt-2">
        <div class="card-header bg-success text-white fw-bold">Your Sessions</div>
        <div class="card-body">
            @if($students->pluck('sessions')->flatten()->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Session Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $counter = 1; @endphp
                            @foreach($students as $student)
                                @foreach($student->sessions as $session)
                                    <tr>
                                        <td>{{ $counter++ }}</td>
                                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($session->session_date)->format('d-m-Y') }}</td>
                                        <td>{{ $session->time_in }}</td>
                                        <td>{{ $session->time_out }}</td>
                                        <td>
                                            <a href="{{ route('teacher.editSession', $session->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('teacher.deleteSession', $session->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger delete-btn" onclick="return confirm('Are you sure you want to delete this session?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">No sessions found.</p>
            @endif
        </div>
    </div>
@endif

</div>
@endsection
