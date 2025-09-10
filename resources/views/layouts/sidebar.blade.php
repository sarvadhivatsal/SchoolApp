<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SchoolApp</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">Management</div>

    <!-- Teachers Dropdown -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTeachers"
            aria-expanded="false" aria-controls="collapseTeachers">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Teachers</span>
        </a>
        <div id="collapseTeachers" class="collapse" aria-labelledby="headingTeachers" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('teachers.create') }}">Add Teacher</a>
                <a class="collapse-item" href="{{ route('teachers.index') }}">All Teachers</a>
            </div>
        </div>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.sessions.index') }}">
            <i class="fas fa-clock"></i>
            <span>Sessions</span>
        </a>
    </li>

    </li>

    <!-- Students Dropdown -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseStudents"
            aria-expanded="false" aria-controls="collapseStudents">
            <i class="fas fa-user-graduate"></i>
            <span>Students</span>
        </a>
        <div id="collapseStudents" class="collapse" aria-labelledby="headingStudents" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('students.create') }}">Add Student</a>
                <a class="collapse-item" href="{{ route('students.index') }}">All Students</a>
                <a class="collapse-item" href="{{ route('student_goals.index') }}">Student Goals</a>
            </div>
        </div>
    </li>

    <!-- Assignments -->
    <!-- Assignments Dropdown -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAssignments"
            aria-expanded="false" aria-controls="collapseAssignments">
            <i class="fas fa-book"></i>
            <span>Assignments</span>
        </a>
        <div id="collapseAssignments" class="collapse" aria-labelledby="headingAssignments"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.assignments.index') }}">All Assignments</a>
                <a class="collapse-item" href="{{ route('admin.assignments.create') }}">Add Assignment</a>
            </div>
        </div>
    </li>

    <!-- Payroll -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.payroll.index') }}">
            <i class="fas fa-money-check-alt"></i>
            <span>Payroll</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
