@extends('layouts.admin')

@section('title', 'Edit Student')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Student</h1>
    </div>

    <div id="alertBox"></div>

    <div class="row">
        <div class="col-md-12">

            <!-- Edit Student Form -->
            <div class="card shadow mb-4">
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.
                            <ul class="mt-2 mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="editStudentForm" method="POST" action="{{ route('students.update', $student->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name', $student->first_name) }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Parent Email</label>
                                <input type="email" name="parent_email" value="{{ old('parent_email', $student->parent_email) }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Parent Phone</label>
                                <input type="text" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone) }}" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>DOB</label>
                                <input type="date" name="dob" value="{{ old('dob', $student->dob) }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $student->gender)=='male'?'selected':'' }}>Male</option>
                                    <option value="female" {{ old('gender', $student->gender)=='female'?'selected':'' }}>Female</option>
                                    <option value="other" {{ old('gender', $student->gender)=='other'?'selected':'' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" {{ old('status', $student->status)=='active'?'selected':'' }}>Active</option>
                                    <option value="inactive" {{ old('status', $student->status)=='inactive'?'selected':'' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Address</label>
                            <textarea name="address" class="form-control">{{ old('address', $student->address) }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>City</label>
                                <input type="text" name="city" value="{{ old('city', $student->city) }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>State</label>
                                <input type="text" name="state" value="{{ old('state', $student->state) }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>Zipcode</label>
                                <input type="text" name="zipcode" value="{{ old('zipcode', $student->zipcode) }}" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Assign Teacher</label>
                            <select name="teacher_id" class="form-select">
                                <option value="">Select Teacher</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id', $student->teacher_id)==$teacher->id?'selected':'' }}>
                                        {{ $teacher->account->first_name }} {{ $teacher->account->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Student</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- All Students DataTable -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-body">
                    <h4>All Students</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="students-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Parent Email</th>
                                    <th>Parent Phone</th>
                                    <th>DOB</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                    <th>City</th>
                                    <th>State</th>
                                    <th>Teacher</th>
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
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

$(document).ready(function () {

    // Initialize DataTable with all fields
    var table = $('#students-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.students.data') }}",
        columns: [
            { data: 'id' },
            { data: null, render: data => data.first_name + ' ' + data.last_name },
            { data: 'parent_email' },
            { data: 'parent_phone' },
            { data: 'dob' },
            { data: 'gender' },
            { data: 'status' },
            { data: 'city' },
            { data: 'state' },
            { data: 'teacher_name' },
            { data: 'action', orderable:false, searchable:false }
        ]
    });

    // Delete confirmation
    $('#students-table').on('click', '.delete-btn', function(e){
        e.preventDefault();
        var form = $(this).closest('form');
        if(confirm('Are you sure you want to delete this student?')) {
            form.submit();
        }
    });

    // AJAX submit for edit form
    $('#editStudentForm').on('submit', function(e){
        e.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            success: function(response){
                $('#alertBox').html('<div class="alert alert-success">Student updated successfully!</div>');
                table.ajax.reload();
            },
            error: function(xhr){
                let errors = xhr.responseJSON?.errors || {};
                let errorHtml = '<div class="alert alert-danger"><ul>';
                $.each(errors, function(key, value){ errorHtml += '<li>'+ value[0] +'</li>'; });
                errorHtml += '</ul></div>';
                $('#alertBox').html(errorHtml);
            }
        });
    });

});
</script>
@endpush
