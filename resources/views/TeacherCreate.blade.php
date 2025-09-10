@extends('layouts.admin')

@section('title', 'Add Teacher')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">Add Teacher</h1>

        <!-- Nav Tabs -->
        <ul class="nav nav-tabs" id="teacherTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="manual-tab" data-toggle="tab" href="#manual" role="tab"
                    aria-controls="manual" aria-selected="true">
                    Add Manually
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="csv-tab" data-toggle="tab" href="#csv" role="tab" aria-controls="csv"
                    aria-selected="false">
                    Import via CSV
                </a>
            </li>
        </ul>

        <div class="tab-content mt-3" id="teacherTabContent">
            <!-- Manual Form -->
            <div class="tab-pane fade show active" id="manual" role="tabpanel" aria-labelledby="manual-tab">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Add Teacher</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('teachers.store') }}" method="POST">
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
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Date of Birth</label>
                                    <input type="date" name="dob" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control">
                                </div>
                                 <div class="col-md-6 mb-3">
                                    <label>Hourly Rate</label>
                                    <input type="number" name="hourly_rate" class="form-control" step="0.01"
                                        min="0" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Gender</label>
                                    <select name="gender" class="form-control">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Address</label>
                                <textarea name="address" class="form-control"></textarea>
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

                            <button type="submit" class="btn btn-primary">Save Teacher</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- CSV Upload Form -->
            <div class="tab-pane fade" id="csv" role="tabpanel" aria-labelledby="csv-tab">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-success">Import Teachers via CSV</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('teachers.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label>Select CSV File</label>
                                <input type="file" name="file" class="form-control" accept=".csv,.xlsx,.xls,.txt"
                                    required>
                            </div>
                            <button type="submit" class="btn btn-success">Upload CSV</button>
                        </form>
                        <div class="mt-3">
                            <p><strong>Note:</strong> CSV headers must match:</p>
                            <code>first_name,last_name,email,password,status,dob,phone,gender,address,city,state,zipcode,hourly_rate</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#teacherTabs a').click(function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });
    </script>
@endpush
