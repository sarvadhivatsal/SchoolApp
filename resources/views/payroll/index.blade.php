@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Payroll</h1>

    <div class="card shadow">
        <div class="card-body">
            <table id="payroll-table" class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Teacher</th>
                        <th>Student</th>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Session Duration (hrs)</th>
                        <th>Session Rate</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" 
      href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

<script>
$(function () {
    const table = $('#payroll-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.payroll.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'teacher_name', name: 'teacher_name' },
            { data: 'student_name', name: 'student_name' },
            { data: 'month', name: 'month' },
            { data: 'year', name: 'year' },
            { 
                data: 'session_duration', 
                name: 'session_duration',
                render: function (data) {
                    return parseFloat(data).toFixed(3); // 3 decimal places
                }
            },
            { data: 'session_rate', name: 'session_rate' },
            { 
                data: 'session_id', 
                name: 'session_id', 
                orderable: false, 
                searchable: false,
                render: function (data) {
                    return `<button class="btn btn-sm btn-warning refresh-btn" data-id="${data}">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>`;
                }
            },
        ],
        order: [[4, 'desc'], [3, 'desc']],
        language: {
            emptyTable: "No payroll data available yet."
        }
    });

    // Handle Refresh Button Click
    $('#payroll-table').on('click', '.refresh-btn', function () {
        const sessionId = $(this).data('id');
        $.ajax({
            url: `/admin/payroll/refresh/${sessionId}`,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                alert(response.message || 'Session payroll refreshed successfully!');
                table.ajax.reload(null, false); // Reload table, stay on current page
            },
            error: function () {
                alert('Failed to refresh payroll for this session.');
            }
        });
    });
});
</script>
@endpush
