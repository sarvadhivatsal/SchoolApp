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
        {{-- ✅ Teacher: standard dropdown (locked, no Select2) --}}
        <div class="form-group mb-3">
            <label>Teacher</label>
            <select name="teacher_id" id="teacher-select" class="form-control" required>
                <option value="">Select Teacher</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">
                        {{ $teacher->account->first_name }} {{ $teacher->account->last_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ✅ Student: Select2-enabled dropdown --}}
        <div class="form-group mb-3">
            <label>Student</label>
            <select name="student_id" id="student-select" class="form-control" required>
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
            <input type="number" name="weekly_mandate" step="0.1" min="0.1" class="form-control"
                value="{{ old('weekly_mandate', $assignment->weekly_mandate ?? '') }}" required>
        </div>

        <div class="form-group mb-3">
            <label>Daily Mandate</label>
            <input type="number" name="daily_mandate" step="1" min="1" class="form-control"
                value="{{ old('daily_mandate', $assignment->daily_mandate ?? '') }}" required>
        </div>

        <button type="submit" class="btn btn-success">Save Assignment</button>
        <a href="{{ route('admin.assignments.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

@push('styles')
    {{-- ✅ Include Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    {{-- ✅ Include Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function () {
        // ✅ Initialize Select2 for Student dropdown
        $('#student-select').select2({
            placeholder: 'Select Student',
            allowClear: true,
            width: '100%'
        });
    });

     $(document).ready(function () {
        // ✅ Initialize Select2 for Teacher dropdown
        $('#teacher-select').select2({
            placeholder: 'Select Teacher',
            allowClear: true,
            width: '100%'
        });
    });
    </script>
@endpush
