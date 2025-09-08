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
                        <label class="form-label">Teacher</label>
                        <input type="text" class="form-control"
                            value="{{ $session->teacher->account->first_name }} {{ $session->teacher->account->last_name }}"
                            disabled>
                    </div>

                    <!-- Student (locked) -->
                    <div class="mb-3">
                        <label class="form-label">Student</label>
                        <input type="text" class="form-control"
                            value="{{ $session->student->first_name }} {{ $session->student->last_name }}" disabled>
                    </div>

                    <!-- Session Date -->
                    <div class="mb-3">
                        <label class="form-label">Session Date</label>
                        <input type="date" name="session_date" class="form-control"
                            value="{{ old('session_date', \Carbon\Carbon::parse($session->session_date)->format('Y-m-d')) }}"
                            required>
                        @error('session_date')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Time In -->
                    <div class="mb-3">
                        <label class="form-label">Time In</label>
                        <input type="time" name="time_in" class="form-control"
                            value="{{ old('time_in', \Carbon\Carbon::parse($session->time_in)->format('H:i')) }}" required>
                        @error('time_in')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Time Out -->
                    <div class="mb-3">
                        <label class="form-label">Time Out</label>
                        <input type="time" name="time_out" class="form-control"
                            value="{{ old('time_out', \Carbon\Carbon::parse($session->time_out)->format('H:i')) }}"
                            required>
                        @error('time_out')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    @foreach ($allGoals as $goal)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="goal_ids[]" value="{{ $goal->id }}"
                                id="goal_{{ $goal->id }}"
                                {{ in_array($goal->id, old('goal_ids', $session->goal_ids ?? [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="goal_{{ $goal->id }}">
                                {{ $goal->short_term_goal }} → {{ $goal->long_term_goal }}
                            </label>
                        </div>
                    @endforeach


                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.sessions.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Session</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
