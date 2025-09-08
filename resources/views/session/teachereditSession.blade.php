@extends('layouts.teacher')

@section('title', 'Edit Session')

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
    <h3>Edit Session</h3>
    <form action="{{ route('teacher.updateSession', $session->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Student</label>
            <select name="student_id" class="form-control" required>
                @foreach($students as $student)
                    <option value="{{ $student->id }}" {{ $session->student_id == $student->id ? 'selected' : '' }}>
                        {{ $student->first_name }} {{ $student->last_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Session Date</label>
            <input type="date" name="session_date" class="form-control" 
                value="{{ \Carbon\Carbon::parse($session->session_date)->format('Y-m-d') }}" required>
        </div>

        <div class="mb-3">
            <label>Time In</label>
            <input type="time" name="time_in" class="form-control" 
                value="{{ \Carbon\Carbon::parse($session->time_in)->format('H:i') }}" required>
        </div>

        <div class="mb-3">
            <label>Time Out</label>
            <input type="time" name="time_out" class="form-control" 
                value="{{ \Carbon\Carbon::parse($session->time_out)->format('H:i') }}" required>
        </div>
        <div class="mb-3">
    <label>Student Goals</label>
    <div class="form-check">
        @foreach($allGoals ?? '' as $goal)
            <div class="mb-1">
                <input type="checkbox" name="goal_ids[]" 
                    value="{{ $goal->id }}" 
                    class="form-check-input"
                    {{ in_array($goal->id, $session->goal_ids ?? []) ? 'checked' : '' }}>
                <label class="form-check-label">
                    {{ $goal->short_term_goal }} - {{ $goal->long_term_goal }}
                </label>
            </div>
        @endforeach
    </div>
</div>


        <button type="submit" class="btn btn-success">Update Session</button>
    </form>
</div>
@endsection
