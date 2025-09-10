@extends('layouts.admin') {{-- SM2 admin layout --}}

@section('title', 'Students Management')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Students Management</h1>
        </div>

        <!-- Nav Tabs -->
        {{-- <ul class="nav nav-tabs mb-4" id="studentTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="add-tab" data-bs-toggle="tab" data-bs-target="#add-student" type="button">Add Student</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="list-tab" data-bs-toggle="tab" data-bs-target="#students-list" type="button">All Students</button>
        </li>
    </ul> --}}

        <div class="tab-content">

            <!-- Add Student Tab -->
            <div class="tab-pane fade show active" id="add-student">
                <div class="card shadow mb-4">
                    <div class="card-body">

                        <ul class="nav nav-tabs" id="addStudentTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" id="manual-tab" data-bs-toggle="tab"
                                    data-bs-target="#manual" type="button">Add Manually</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="csv-tab" data-bs-toggle="tab" data-bs-target="#csv"
                                    type="button">Import via CSV</button>
                            </li>
                        </ul>

                        <div class="tab-content mt-3">

                            <!-- Manual Form -->
                            <div class="tab-pane fade show active" id="manual">
                                <form action="{{ route('students.store') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>First Name</label>
                                            <input type="text" name="first_name" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Last Name</label>
                                            <input type="text" name="last_name" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>Parent Email</label>
                                            <input type="email" name="parent_email" class="form-control">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Parent Phone</label>
                                            <input type="text" name="parent_phone" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label>Date of Birth</label>
                                            <input type="date" name="dob" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Gender</label>
                                            <select name="gender" class="form-select">
                                                <option value="">Select Gender</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Status</label>
                                            <select name="status" class="form-select">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label>Address</label>
                                        <input type="text" name="address" class="form-control">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label>City</label>
                                            <input type="text" name="city" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>State</label>
                                            <input type="text" name="state" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Zipcode</label>
                                            <input type="text" name="zipcode" class="form-control">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label>Assign Teacher</label>
                                        <select name="teacher_id" class="form-select">
                                            <option value="">Select Teacher</option>
                                            @foreach ($teachers as $teacher)
                                                <option value="{{ $teacher->id }}">
                                                    {{ $teacher->account->first_name }} {{ $teacher->account->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>Daily Mandate (hours)</label>
                                            <input type="number" step="0.25" name="daily_mandate" class="form-control"
                                                required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Weekly Mandate (hours)</label>
                                            <input type="number" step="0.25" name="weekly_mandate" class="form-control"
                                                required>
                                        </div>
                                    </div>


                                    <button type="submit" class="btn btn-primary">Save Student</button>
                                </form>
                            </div>

                            <!-- CSV Upload Form -->
                            <div class="tab-pane fade" id="csv">
                                <form action="{{ route('students.import') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label>Select CSV File</label>
                                        <input type="file" name="file" class="form-control"
                                            accept=".csv,.xlsx,.xls,.txt" required>
                                    </div>
                                    <button type="submit" class="btn btn-success">Upload CSV</button>
                                </form>
                                <div class="mt-3">
                                    <p><strong>Note:</strong> CSV headers must match:</p>
                                    <code>first_name,last_name,parent_email,parent_phone,status,dob,gender,address,city,state,zipcode,teacher_id,daily_mandate,weekly_mandate</code>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- All Students Tab -->
            <div class="tab-pane fade" id="students-list">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="students-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Parent Email</th>
                                        <th>Parent Phone</th>
                                        <th>Status</th>
                                        <th>Daily Mandate</th>
                                        <th>Weekly Mandate</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#students-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.students.data') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: null,
                        render: data => data.first_name + ' ' + data.last_name
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
                        data: 'status',
                        name: 'status'
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
