@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Add Assignment</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.assignments.store') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label>Teacher</label>
            <select name="teacher_id" class="form-control" required>
                <option value="">Select Teacher</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">
                        {{ $teacher->account->first_name }} {{ $teacher->account->last_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Student</label>
            <select name="student_id" class="form-control" required>
                <option value="">Select Student</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}">
                        {{ $student->first_name }} {{ $student->last_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Start Date</label>
            <input type="date" name="start_date" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>End Date</label>
            <input type="date" name="end_date" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>Weekly Mandate</label>
            <input type="number" name="weekly_mandate" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>Daily Mandate</label>
            <input type="number" name="daily_mandate" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Save Assignment</button>
        <a href="{{ route('admin.assignments.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
