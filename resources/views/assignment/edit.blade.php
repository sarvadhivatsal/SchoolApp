@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Edit Assignment</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.assignments.update', $assignment->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label>Teacher</label>
            <select name="teacher_id" class="form-control" required>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ $assignment->teacher_id == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->account->first_name }} {{ $teacher->account->last_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Student</label>
            <select name="student_id" class="form-control" required>
                @foreach($students as $student)
                    <option value="{{ $student->id }}" {{ $assignment->student_id == $student->id ? 'selected' : '' }}>
                        {{ $student->first_name }} {{ $student->last_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Start Date</label>
            <input type="date" name="start_date" value="{{ $assignment->start_date }}" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>End Date</label>
            <input type="date" name="end_date" value="{{ $assignment->end_date }}" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>Weekly Mandate</label>
            <input type="number" name="weekly_mandate" step="0.1" min="0.1"
           value="{{ old('weekly_mandate', $assignment->weekly_mandate) }}" 
           class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>Daily Mandate</label>
         <input type="number" name="daily_mandate" step="1" min="1"
           value="{{ old('daily_mandate', $assignment->daily_mandate) }}" 
           class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Assignment</button>
        <a href="{{ route('admin.assignments.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
