@extends('layouts.admin')

@section('title', 'Assignments Management')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">All Assignments</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.assignments.create') }}" class="btn btn-primary mb-3">+ Add Assignment</a>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="assignments-table">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Teacher</th>
                    <th>Student</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Weekly Mandate</th>
                    <th>Daily Mandate</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#assignments-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('assignments.data') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'teacher_name', name: 'teacher_name' },
            { data: 'student_name', name: 'student_name' },
            { data: 'start_date', name: 'start_date' },
            { data: 'end_date', name: 'end_date' },
            { data: 'weekly_mandate', name: 'weekly_mandate' },
            { data: 'daily_mandate', name: 'daily_mandate' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Delete confirmation for dynamically generated buttons
    $('#assignments-table').on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        if(confirm('Are you sure you want to delete this assignment?')){
            form.submit();
        }
    });
});
</script>
@endpush
