@extends('layouts.admin')

@section('title', 'Edit Session')

@section('content')
    <div class="container-fluid">
        <h3 class="mb-4">Edit Session</h3>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.sessions.update', $session->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Teacher (locked) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teacher</label>
                        <input type="text" class="form-control"
                            value="{{ $session->teacher->account->first_name }} {{ $session->teacher->account->last_name }}"
                            disabled>
                    </div>

                    <!-- Student(s) (locked) -->
                    <!-- Student(s) -->
                    <!-- Student(s) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Student(s)</label>
                        <select name="student_ids[]" class="form-control" multiple required>
                            @foreach ($allStudents as $student)
                                <option value="{{ $student->id }}"
                                    {{ in_array($student->id, $selectedStudents ?? []) ? 'selected' : '' }}>
                                    {{ $student->first_name }} {{ $student->last_name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            Hold Ctrl (Windows) or Cmd (Mac) to select multiple students.
                        </small>
                    </div>


                    <!-- Session Date -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Session Date</label>
                        <input type="date" name="session_date" class="form-control"
                            value="{{ old('session_date', \Carbon\Carbon::parse($session->session_date)->format('Y-m-d')) }}"
                            required>
                        @error('session_date')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Time In -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Time In</label>
                        <input type="time" id="time_in" name="time_in" class="form-control"
                            value="{{ old('time_in', \Carbon\Carbon::parse($session->time_in)->format('H:i')) }}" required>
                        @error('time_in')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Time Out -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Time Out</label>
                        <input type="time" id="time_out" name="time_out" class="form-control"
                            value="{{ old('time_out', \Carbon\Carbon::parse($session->time_out)->format('H:i')) }}"
                            required>
                        @error('time_out')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Session Rate -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Session Rate ($)</label>
                        <input type="text" id="session_rate" class="form-control"
                            value="{{ number_format($session->session_rate, 2) }}" readonly>
                        <small class="text-muted">
                            Calculated from teacher's hourly rate × session duration
                        </small>
                    </div>

                    <!-- Goals -->
                    <!-- Goals -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Goals</label>
                        @if ($allGoals->isEmpty())
                            <p class="text-danger">
                                These students currently have no goals.
                                <a href="{{ route('student_goals.create') }}" class="btn btn-sm btn-outline-primary">Create
                                    Goal</a>
                            </p>
                        @else
                            <div class="row">
                                @foreach ($allGoals as $goal)
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="goal_ids[]"
                                                value="{{ $goal->id }}" id="goal_{{ $goal->id }}"
                                                {{ in_array($goal->id, old('goal_ids', $session->goal_ids ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="goal_{{ $goal->id }}">
                                                {{ $goal->short_term_goal }} → {{ $goal->long_term_goal }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>


                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('admin.sessions.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Session</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let teacherHourlyRate = {{ $session->teacher->hourly_rate ?? 0 }};

            function calculateSessionRate() {
                const timeIn = document.getElementById('time_in').value;
                const timeOut = document.getElementById('time_out').value;
                const rateField = document.getElementById('session_rate');

                if (!timeIn || !timeOut || teacherHourlyRate <= 0) {
                    rateField.value = '';
                    return;
                }

                const start = new Date(`1970-01-01T${timeIn}:00`);
                const end = new Date(`1970-01-01T${timeOut}:00`);
                const diffHours = (end - start) / (1000 * 60 * 60);

                if (diffHours > 0) {
                    rateField.value = (teacherHourlyRate * diffHours).toFixed(2);
                } else {
                    rateField.value = '';
                }
            }

            document.getElementById('time_in').addEventListener('change', calculateSessionRate);
            document.getElementById('time_out').addEventListener('change', calculateSessionRate);
        });
    </script>
@endsection
