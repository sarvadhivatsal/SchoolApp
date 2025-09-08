@extends('layouts.teacher')
@section('title','Add Session')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container">
    <h3 class="mb-4">Add Session</h3>

    <form action="{{ route('teacher.storeSession') }}" method="POST">
        @csrf
        <input type="hidden" name="teacher_id" value="{{ auth('teacher')->id() }}">

        <!-- Student Dropdown -->
        <div class="mb-3">
            <label class="form-label">Student</label>
            <select name="student_id" class="form-control" required>
                <option value="">Select Student</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Session Date -->
        <div class="mb-3">
            <label class="form-label">Session Date</label>
            <input type="date" name="session_date" class="form-control" required>
        </div>

        <!-- Time In -->
        <div class="mb-3">
            <label class="form-label">Time In</label>
            <input type="time" name="time_in" class="form-control" required>
        </div>

        <!-- Time Out -->
        <div class="mb-3">
            <label class="form-label">Time Out</label>
            <input type="time" name="time_out" class="form-control" required>
        </div>

        <!-- Goals Selection -->
        <div class="mb-3">
            <label class="form-label">Select Goals</label>
            @if($allGoals->isNotEmpty())
                @foreach($allGoals as $goal)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" 
                               name="goal_ids[]" 
                               value="{{ $goal->id }}" 
                               id="goal_{{ $goal->id }}">
                        <label class="form-check-label" for="goal_{{ $goal->id }}">
                            {{ $goal->short_term_goal }} → {{ $goal->long_term_goal }}
                        </label>
                    </div>
                @endforeach
            @else
                <p>No goals available.</p>
            @endif
        </div>

        <button type="submit" class="btn btn-success">Add Session</button>
    </form>
</div>
@endsection
