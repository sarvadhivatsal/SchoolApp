<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('teacher.dashboard') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Teacher</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <!-- Assignments -->
<li class="nav-item {{ request()->query('section', 'assignments') == 'assignments' ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('teacher.dashboard', ['section' => 'assignments']) }}">
        <i class="fas fa-fw fa-tasks"></i>
        <span>Assignments</span>
    </a>
</li>

<!-- Sessions -->
<li class="nav-item {{ request()->query('section') == 'sessions' ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('teacher.dashboard', ['section' => 'sessions']) }}">
        <i class="fas fa-fw fa-calendar"></i>
        <span>Sessions</span>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('teacher.student_goals.index') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('teacher.student_goals.index') }}">
        <i class="fas fa-bullseye"></i>
        <span>Student Goals</span>
    </a>
</li>


    <hr class="sidebar-divider d-none d-md-block">

    <!-- Logout -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </li>
</ul>
