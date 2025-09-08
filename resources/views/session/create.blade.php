@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Create Session</h4>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.sessions.store') }}" method="POST">
                @csrf

                {{-- Teacher --}}
                <div class="mb-3">
                    <label for="teacher_id" class="form-label">Teacher</label>
                    <select id="teacher_id" name="teacher_id" class="form-select" required>
                        <option value="">Select Teacher</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">
                                {{ $teacher->account->first_name }} {{ $teacher->account->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Student --}}
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student</label>
                    <select id="student_id" name="student_id" class="form-select" required>
                        <option value="">Select Student</option>
                    </select>
                </div>

                {{-- Goals checkboxes --}}
                <div class="mb-3" id="goals-container" style="display:none;">
                    <label class="form-label"><strong>Select Goals</strong></label>
                    <div id="goal-list" class="border rounded p-2 bg-light"></div>
                </div>

                {{-- Session date/time --}}
                <div class="mb-3">
                    <label for="session_date" class="form-label">Session Date</label>
                    <input type="date" id="session_date" name="session_date" class="form-control" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="time_in" class="form-label">Time In</label>
                        <input type="time" id="time_in" name="time_in" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="time_out" class="form-label">Time Out</label>
                        <input type="time" id="time_out" name="time_out" class="form-control" required>
                    </div>
                </div>

                <button class="btn btn-success" type="submit">Create Session</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const studentsBase = "{{ url('admin/sessions/get-students') }}";
    const goalsBase    = "{{ url('admin/sessions/get-goals') }}";

    const teacherSelect = document.getElementById('teacher_id');
    const studentSelect = document.getElementById('student_id');
    const goalsContainer = document.getElementById('goals-container');
    const goalList = document.getElementById('goal-list');

    teacherSelect.addEventListener('change', function () {
        const teacherId = this.value;
        studentSelect.innerHTML = '<option value="">Select Student</option>';
        goalList.innerHTML = '';
        goalsContainer.style.display = 'none';

        if (!teacherId) return;

        fetch(`${studentsBase}/${teacherId}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = `${s.first_name} ${s.last_name}`;
                    studentSelect.appendChild(opt);
                });
            });
    });

    studentSelect.addEventListener('change', function () {
        const studentId = this.value;
        goalList.innerHTML = '';
        goalsContainer.style.display = 'none';

        if (!studentId) return;

        fetch(`${goalsBase}/${studentId}`)
            .then(r => r.json())
            .then(goals => {
                if (!Array.isArray(goals) || goals.length === 0) {
                    goalList.innerHTML = '<p class="text-muted">No goals available.</p>';
                } else {
                    goals.forEach(goal => {
                        const div = document.createElement('div');
                        div.className = 'form-check mb-2';
                        div.innerHTML = `
                            <input class="form-check-input" type="checkbox" name="goal_ids[]" value="${goal.id}" id="goal_${goal.id}">
                            <label class="form-check-label" for="goal_${goal.id}">
                                ${goal.short_term_goal} → ${goal.long_term_goal}
                            </label>
                        `;
                        goalList.appendChild(div);
                    });
                }
                goalsContainer.style.display = 'block';
            });
    });
});
</script>
@endsection
