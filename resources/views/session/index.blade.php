@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Sessions</h2>

    {{-- ✅ Success Message --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- ✅ Action Buttons --}}
    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('admin.sessions.create') }}" class="btn btn-primary">➕ Add New Session</a>
        <a href="{{ route('admin.sessions.export') }}" class="btn btn-success">📤 Export CSV</a>
    </div>

    {{-- ✅ Filters --}}
    <div class="row mb-3 g-3">
        <div class="col-md-3">
            <label class="form-label fw-semibold">Teacher</label>
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
            <label class="form-label fw-semibold">Student</label>
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
            <label class="form-label fw-semibold">Date</label>
            <input type="date" id="filter-date" class="form-control">
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <button id="filter-reset" class="btn btn-secondary w-100">🔄 Reset Filters</button>
        </div>
    </div>

    {{-- ✅ DataTable --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle" id="sessions-table">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Teacher</th>
                    <th>Student(s)</th>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Session Rate ($)</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('styles')
    {{-- ✅ Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    {{-- ✅ Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(function () {
        // ✅ Initialize Select2 for filters
        $('#filter-teacher').select2({
            placeholder: 'Filter by Teacher',
            allowClear: true,
            width: '100%'
        });
        $('#filter-student').select2({
            placeholder: 'Filter by Student',
            allowClear: true,
            width: '100%'
        });

        // ✅ Initialize DataTable
        const table = $('#sessions-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.sessions.index') }}",
                data: function (d) {
                    d.teacher_id   = $('#filter-teacher').val() || '';
                    d.student_id   = $('#filter-student').val() || '';
                    d.session_date = $('#filter-date').val() || '';
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'teacher_name', name: 'teacher.account.first_name' },
                { data: 'student_name', name: 'student.first_name' },
                { data: 'session_date', name: 'session_date' },
                { data: 'time_in', name: 'time_in' },
                { data: 'time_out', name: 'time_out' },
                { data: 'session_rate', name: 'session_rate', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[3, 'desc']],
            pageLength: 10,
            responsive: true,
            language: {
                search: "🔍 Search:",
                lengthMenu: "Show _MENU_ entries",
                zeroRecords: "No matching sessions found",
                info: "Showing _START_ to _END_ of _TOTAL_ sessions",
                infoEmpty: "No sessions available",
                infoFiltered: "(filtered from _MAX_ total sessions)"
            }
        });

        // ✅ Reload table when filters change
        $('#filter-teacher, #filter-student, #filter-date').on('change', () => table.ajax.reload());

        // ✅ Reset filters
        $('#filter-reset').on('click', function () {
            $('#filter-teacher').val(null).trigger('change');
            $('#filter-student').val(null).trigger('change');
            $('#filter-date').val('');
            table.ajax.reload();
        });
    });
    </script>
@endpush
