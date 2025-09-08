@extends(auth()->guard('admin')->check() ? 'layouts.admin' : 'layouts.teacher')

@section('title', 'Student Goals')

@section('content')
<div class="container-fluid">
    <h3 class="mb-3">Student Goals</h3>

    @if(auth('admin')->check())
        <a href="{{ route('student_goals.create') }}" class="btn btn-success mb-3">Add Goal</a>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Student</th>
                <th>Short Term Goal</th>
                <th>Long Term Goal</th>
                @if(auth('admin')->check())
                    <th>Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($goals as $goal)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $goal->student->first_name }} {{ $goal->student->last_name }}</td>
                <td>{{ $goal->short_term_goal }}</td>
                <td>{{ $goal->long_term_goal }}</td>

                @if(auth('admin')->check())
                    <td>
                        <a href="{{ route('student_goals.edit', $goal->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('student_goals.destroy', $goal->id) }}" method="POST" style="display:inline;">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
