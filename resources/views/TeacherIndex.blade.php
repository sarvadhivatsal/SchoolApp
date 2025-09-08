@extends('layouts.admin')

@section('title', 'Teachers Management')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Teachers Management</h1>
</div>

<!-- Nav Tabs -->
<ul class="nav nav-tabs mb-4" id="teacherTabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="list-tab" data-bs-toggle="tab" href="#list" role="tab">All Teachers</a>
    </li>
</ul>

<!-- All Teachers Tab -->
<div class="tab-pane fade show active" id="list" role="tabpanel">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Teachers</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="teachers-table" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate data -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#teachers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.teachers.data') }}",
        columns: [
            { data: 'id', name: 'id' },
            { 
                data: null, 
                render: function(data) {
                    return data.account.first_name + ' ' + data.account.last_name;
                } 
            },
            { data: 'account.email', name: 'account.email' },
            { data: 'phone', name: 'phone' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Delete confirmation
    $('#teachers-table').on('click', '.delete-btn', function(e) {
        e.preventDefault(); // prevent default form submit
        var form = $(this).closest('form');
        if (confirm('Are you sure you want to delete this teacher?')) {
            form.submit(); // submit form if confirmed
        }
    });
});
</script>
@endpush
