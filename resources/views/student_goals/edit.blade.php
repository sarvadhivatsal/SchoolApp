@extends('layouts.admin') {{-- Or your main layout --}}

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Student Goal</h2>

    {{-- Show validation errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Edit Form --}}
    <form action="{{ route('student_goals.update', $goal->id) }}" method="POST">
        @csrf
        {{-- @method('PUT') --}}

        {{-- Student (Dropdown) --}}
        <div class="mb-3">
            <label class="form-label">Student</label>
            <select name="student_id" class="form-control" disabled>
                @foreach($students as $student)
                    <option value="{{ $student->id }}" 
                        {{ $goal->student_id == $student->id ? 'selected' : '' }}>
                        {{ $student->first_name }} {{ $student->last_name }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">You cannot change the student for this goal.</small>
        </div>

        {{-- Short Term Goal --}}
        <div class="mb-3">
            <label class="form-label">Short Term Goal</label>
            <input type="text" name="short_term_goal" class="form-control"
                value="{{ old('short_term_goal', $goal->short_term_goal) }}" required>
            @error('short_term_goal')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        {{-- Long Term Goal --}}
        <div class="mb-3">
            <label class="form-label">Long Term Goal</label>
            <input type="text" name="long_term_goal" class="form-control"
                value="{{ old('long_term_goal', $goal->long_term_goal) }}" required>
            @error('long_term_goal')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-success">Update Goal</button>
        <a href="{{ route('student_goals.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
