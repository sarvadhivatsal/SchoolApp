@extends('layouts.admin')

@section('title', 'Edit Teacher')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Edit Teacher</h1>

    <!-- Section Toggle Buttons -->
    <div class="mb-3">
        <button class="btn btn-secondary btn-sm" onclick="showSection('edit-teacher')">Edit Teacher</button>
        <button class="btn btn-success btn-sm" onclick="showSection('teachers')">All Teachers</button>
    </div>

    <!-- All Teachers Section -->
    <div id="teachers-section" style="display:none;">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">All Teachers</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="teachers-table">
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
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Teacher Section -->
    <div id="edit-teacher-section">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Edit Teacher</h6>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

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

                <form method="POST" action="{{ route('teachers.update', $teacher->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $teacher->account->first_name) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $teacher->account->last_name) }}" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email', $teacher->account->email) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">DOB</label>
                            <input type="date" name="dob" value="{{ old('dob', $teacher->dob) }}" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $teacher->gender)=='male'?'selected':'' }}>Male</option>
                                <option value="female" {{ old('gender', $teacher->gender)=='female'?'selected':'' }}>Female</option>
                                <option value="other" {{ old('gender', $teacher->gender)=='other'?'selected':'' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', $teacher->account->status)=='active'?'selected':'' }}>Active</option>
                                <option value="inactive" {{ old('status', $teacher->account->status)=='inactive'?'selected':'' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control">{{ old('address', $teacher->address) }}</textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" value="{{ old('city', $teacher->city) }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" name="state" value="{{ old('state', $teacher->state) }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Zipcode</label>
                            <input type="text" name="zipcode" value="{{ old('zipcode', $teacher->zipcode) }}" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Teacher</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
function showSection(section) {
    $('#edit-teacher-section, #teachers-section').hide();
    $('#' + section + '-section').show();

    if(section === 'teachers' && !$.fn.DataTable.isDataTable('#teachers-table')){
        $('#teachers-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.teachers.data') }}",
            columns: [
                {data: 'id'},
                {data: 'teacher_name'},
                {data: 'email'},
                {data: 'phone'},
                {data: 'status'},
                {data: 'action', orderable: false, searchable: false}
            ]
        });
    }
}

// Default: Show Edit Teacher Section
$(document).ready(function(){
    showSection('edit-teacher');
});
</script>
@endpush
