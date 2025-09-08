@extends('layouts.admin')
@section('title', 'Add Student Goal')

@section('content')
<div class="container">
    <h3>Add Goal</h3>
    <form action="{{ route('student_goals.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Student</label>
            <select name="student_id" class="form-control" required>
                <option value="">Select Student</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Short Term Goal</label>
            <input type="text" name="short_term_goal" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Long Term Goal</label>
            <input type="text" name="long_term_goal" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>
@endsection
