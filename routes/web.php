<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\StudentGoalController;

// ------------------ Public Routes ------------------
Route::get('/', function () {
    return redirect()->route('login');
});

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// ------------------ Admin Routes ------------------
Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::get('/admindashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Teacher CRUD
    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
    Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
    Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
    Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{teacher}/delete', [TeacherController::class, 'destroy'])->name('teachers.destroy');

    // Student CRUD
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

    // Assignment CRUD
    Route::get('/assignments', [AssignmentController::class, 'index'])->name('admin.assignments.index');
    Route::get('/assignments/create', [AssignmentController::class, 'create'])->name('admin.assignments.create');
    Route::post('/assignments', [AssignmentController::class, 'store'])->name('admin.assignments.store');
    Route::get('/assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('admin.assignments.edit');
    Route::put('/assignments/{assignment}', [AssignmentController::class, 'update'])->name('admin.assignments.update');
    Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('admin.assignments.destroy');

    // DataTables JSON
    Route::get('/teachers/data', [TeacherController::class, 'getTeachers'])->name('admin.teachers.data');
    Route::get('/students/data', [StudentController::class, 'getStudents'])->name('admin.students.data');
    Route::get('/assignments/data', [AssignmentController::class, 'getData'])->name('assignments.data');

    // CSV Import
    Route::post('/teachers/import', [TeacherController::class, 'import'])->name('teachers.import');
    Route::post('/student/import', [StudentController::class, 'import'])->name('students.import');

    // ------------------ Session CRUD ------------------
    Route::get('/sessions', [SessionController::class, 'index'])->name('admin.sessions.index');
    Route::get('/sessions/create', [SessionController::class, 'create'])->name('admin.sessions.create');
    Route::post('/sessions', [SessionController::class, 'store'])->name('admin.sessions.store');
    Route::get('/sessions/{session}/edit', [SessionController::class, 'edit'])->name('admin.sessions.edit');
    Route::put('/sessions/{session}', [SessionController::class, 'update'])->name('admin.sessions.update');
    Route::delete('/sessions/{session}', [SessionController::class, 'destroy'])->name('admin.sessions.destroy');

    Route::get('/sessions/get-students/{teacherId}', [SessionController::class, 'getStudents'])
        ->name('sessions.getStudents');

    Route::get('/sessions/get-goals/{studentId}', [SessionController::class, 'getGoals'])
        ->name('sessions.getGoals');

    // AJAX route to get students for a teacher
    Route::get('/sessions/get-students/{teacherId}', [SessionController::class, 'getStudents'])
        ->name('sessions.getStudents');

    // routes/web.php
    Route::get('/admin/sessions/export', [App\Http\Controllers\SessionController::class, 'export'])
        ->name('admin.sessions.export');


    Route::get('/student_goals', [StudentGoalController::class, 'index'])->name('student_goals.index');
    Route::get('/student_goals/create', [StudentGoalController::class, 'create'])->name('student_goals.create');
    Route::post('/student_goals/store', [StudentGoalController::class, 'store'])->name('student_goals.store');
    Route::get('/student_goals/{id}/edit', [StudentGoalController::class, 'edit'])->name('student_goals.edit');
    Route::post('/student_goals/{id}/update', [StudentGoalController::class, 'update'])->name('student_goals.update');
    Route::delete('/student_goals/{id}/delete', [StudentGoalController::class, 'destroy'])->name('student_goals.destroy');

    Route::get('/admin/sessions/get-goals/{studentId}', [SessionController::class, 'getGoals']);
});


// ------------------ Teacher Routes ------------------
Route::middleware(['teacher'])->prefix('teacher')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');

    // Teacher’s DataTables
    Route::get('/assignments/data', [TeacherController::class, 'getAssignmentData'])->name('teacher.assignments.data');
    Route::get('/students/data', [StudentController::class, 'getTeacherStudents'])->name('teacher.students.data');

    Route::get('/session/teacher_session', [TeacherController::class, 'createSession'])->name('teacher.createSession');
    Route::post('/session/store', [TeacherController::class, 'storeSession'])->name('teacher.storeSession');
    Route::get('/session/{session}/edit', [TeacherController::class, 'editSession'])->name('teacher.editSession');
    Route::put('/session/{session}', [TeacherController::class, 'updateSession'])->name('teacher.updateSession');
    Route::delete('/session/{session}', [TeacherController::class, 'deleteSession'])->name('teacher.deleteSession');
    Route::get('/session/teacher_session', [TeacherController::class, 'createSession'])->name('teacher.createSession');

    // Route::get('/student_goals', [StudentGoalController::class, 'index'])->name('student_goals.index');
    // Route::get('/student_goals/create', [StudentGoalController::class, 'create'])->name('student_goals.create');
    // Route::post('/student_goals/store', [StudentGoalController::class, 'store'])->name('student_goals.store');
    // Route::get('/student_goals/{id}/edit', [StudentGoalController::class, 'edit'])->name('student_goals.edit');
    // Route::post('/student_goals/{id}/update', [StudentGoalController::class, 'update'])->name('student_goals.update');
    // Route::delete('/student_goals/{id}/delete', [StudentGoalController::class, 'destroy'])->name('student_goals.destroy');
    Route::get('/student_goals', [StudentGoalController::class, 'index'])->name('teacher.student_goals.index');
});
