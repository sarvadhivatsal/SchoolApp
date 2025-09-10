@extends('layouts.admin') {{-- Your SM2 admin layout --}}

@section('title', 'All Students')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">All Students</h1>
        </div>

        <!-- Students Table Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Student List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="students-table" width="100%"
                        cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>DOB</th>
                                <th>Gender</th>
                                <th>Status</th>
                                <th>Parent Email</th>
                                <th>Parent Phone</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Teacher</th>
                                <th>Daily Mandate</th>
                                <th>Weekly Mandate</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody> {{-- DataTables will populate --}}
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <!-- jQuery, Bootstrap & DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#students-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('admin.students.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'first_name',
                        name: 'first_name'
                    },
                    {
                        data: 'last_name',
                        name: 'last_name'
                    },
                    {
                        data: 'dob',
                        name: 'dob'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'parent_email',
                        name: 'parent_email'
                    },
                    {
                        data: 'parent_phone',
                        name: 'parent_phone'
                    },
                    {
                        data: 'city',
                        name: 'city'
                    },
                    {
                        data: 'state',
                        name: 'state'
                    },
                    {
                        data: 'teacher_name',
                        name: 'teacher_name'
                    },
                    {
                        data: 'daily_mandate',
                        name: 'daily_mandate'
                    },
                    {
                        data: 'weekly_mandate',
                        name: 'weekly_mandate'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Delete confirmation
            $('#students-table').on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                if (confirm('Are you sure you want to delete this student?')) {
                    form.submit();
                }
            });
        });
    </script>
@endpush
