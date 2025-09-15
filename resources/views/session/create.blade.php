@extends('layouts.admin')

@section('title', 'Create Session')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Create Session</h4>
        </div>
        <div class="card-body">

            {{-- Validation Errors --}}
            @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.sessions.store') }}" method="POST">
                @csrf

                {{-- Teacher --}}
                <div class="mb-3">
                    <label class="form-label">Teacher</label>
                    <select id="teacher_id" name="teacher_id" class="form-select" required>
                        <option value="">Select Teacher</option>
                        @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}">
                            {{ $teacher->account->first_name }} {{ $teacher->account->last_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Students --}}
                <div class="mb-3">
                    <label class="form-label">Students</label>
                    <select id="student_ids" name="student_ids[]" class="form-select" multiple="multiple" required>
                        {{-- Options loaded dynamically --}}
                    </select>
                    <small class="text-muted">Select one or more students. Start typing to search.</small>
                </div>

                {{-- Goals --}}
                <div class="mb-3" id="goals-container" style="display:none;">
                    <label class="form-label"><strong>Select Goals</strong></label>
                    <div id="goal-list" class="border rounded p-2 bg-light"></div>
                </div>

                {{-- Session Date --}}
                <div class="mb-3">
                    <label class="form-label">Session Date</label>
                    <input type="date" id="session_date" name="session_date" class="form-control" required>
                </div>

                {{-- Time --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Time In</label>
                        <input type="time" id="time_in" name="time_in" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Time Out</label>
                        <input type="time" id="time_out" name="time_out" class="form-control" required>
                    </div>
                </div>

                {{-- Session Rate --}}
                <div class="mb-3">
                    <label class="form-label">Session Rate ($)</label>
                    <input type="text" id="session_rate" class="form-control" readonly>
                    <small class="text-muted">Calculated from teacher's hourly rate × session duration</small>
                </div>

                <button class="btn btn-success" type="submit">Create Session</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Include jQuery & Select2 JS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    const studentsUrl = "{{ url('admin/sessions/get-students') }}";
    const goalsUrl = "{{ url('admin/sessions/get-goals') }}";

    let teacherRate = 0;

    // Initialize Select2
    $('#teacher_id').select2({ placeholder: 'Select Teacher', allowClear: true, width: '100%' });
    const studentSelect = $('#student_ids').select2({ placeholder: 'Select Students', allowClear: true, width: '100%' });

    // Calculate session rate
    const calculateRate = () => {
        const start = $('#time_in').val();
        const end = $('#time_out').val();
        if (!start || !end || teacherRate <= 0) return $('#session_rate').val('');
        const diff = (new Date(`1970-01-01T${end}`) - new Date(`1970-01-01T${start}`)) / 3600000;
        $('#session_rate').val(diff > 0 ? (diff * teacherRate).toFixed(2) : '');
    };

    // Fetch students dynamically
    $('#teacher_id').on('change', function() {
        const teacherId = $(this).val();
        studentSelect.empty().trigger('change');
        $('#goal-list').empty(); $('#goals-container').hide();
        teacherRate = 0; $('#session_rate').val('');

        if (!teacherId) return;

        fetch(`${studentsUrl}/${teacherId}`)
            .then(res => res.json())
            .then(data => {
                if (!data.length) {
                    studentSelect.append('<option disabled>No students assigned</option>');
                } else {
                    data.forEach(s => {
                        const option = new Option(`${s.first_name} ${s.last_name}`, s.id, false, false);
                        studentSelect.append(option);
                    });
                }
                studentSelect.trigger('change');
            });

        // Fetch teacher rate
        fetch(`/admin/teachers/${teacherId}/rate`)
            .then(res => res.json())
            .then(data => { teacherRate = parseFloat(data.hourly_rate) || 0; calculateRate(); });
    });

    // Fetch goals for selected student
    studentSelect.on('change', function() {
        const selectedIds = $(this).val();
        const goalList = $('#goal-list');
        if (!selectedIds || !selectedIds.length) {
            goalList.empty(); $('#goals-container').hide(); return;
        }

        fetch(`${goalsUrl}/${selectedIds[0]}`)
            .then(res => res.json())
            .then(goals => {
                goalList.empty();
                if (!goals.length) {
                    goalList.html(`<p class="text-muted">No goals created for this student.</p>
                        <a href="{{ route('student_goals.create') }}" class="btn btn-sm btn-outline-primary">Create Goal</a>`);
                } else {
                    goals.forEach(g => {
                        goalList.append(`
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="goal_ids[]" value="${g.id}" id="goal_${g.id}">
                                <label class="form-check-label" for="goal_${g.id}">${g.short_term_goal} → ${g.long_term_goal}</label>
                            </div>
                        `);
                    });
                }
                $('#goals-container').show();
            });
    });

    $('#time_in, #time_out').on('change', calculateRate);
});
</script>
@endpush
