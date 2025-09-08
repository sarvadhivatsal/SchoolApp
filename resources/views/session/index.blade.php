@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Sessions</h2>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-3">
        <a href="{{ route('admin.sessions.create') }}" class="btn btn-primary">Add New Session</a>
    </div>
    <div class="mb-3">
    {{-- <a href="{{ route('admin.sessions.create') }}" class="btn btn-primary">Add New Session</a> --}}
    <a href="{{ route('admin.sessions.export') }}" class="btn btn-success">Export CSV</a>
</div>


    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-md-3">
            <label>Teacher</label>
            <select id="filter-teacher" class="form-control">
                <option value="">All</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">
                        {{ $teacher->account->first_name }} {{ $teacher->account->last_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label>Student</label>
            <select id="filter-student" class="form-control">
                <option value="">All</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}">
                        {{ $student->first_name }} {{ $student->last_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label>Date</label>
            <input type="date" id="filter-date" class="form-control">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button id="filter-reset" class="btn btn-secondary w-100">Reset</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="sessions-table">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Teacher</th>
                    <th>Student</th>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    let table = $('#sessions-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.sessions.index') }}",
            data: function (d) {
                d.teacher_id = $('#filter-teacher').val();
                d.student_id = $('#filter-student').val();
                d.session_date = $('#filter-date').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'teacher_name', name: 'teacher.account.first_name' },
            { data: 'student_name', name: 'student.first_name' },
            { data: 'session_date', name: 'session_date' },
            { data: 'time_in', name: 'time_in' },
            { data: 'time_out', name: 'time_out' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Apply filters
    $('#filter-teacher, #filter-student, #filter-date').on('change', function () {
        table.ajax.reload();
    });

    // Reset filters
    $('#filter-reset').on('click', function () {
        $('#filter-teacher').val('');
        $('#filter-student').val('');
        $('#filter-date').val('');
        table.ajax.reload();
    });
});
</script>
@endpush
