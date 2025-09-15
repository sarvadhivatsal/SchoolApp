<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'student_id',
        'session_date',
        'time_in',
        'time_out',
        'goal_ids',
        'session_rate',
    ];

    protected $casts = [
        'goal_ids' => 'array', // JSON -> array automatically
    ];

    // Relation to Teacher
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

    // Relation to single Student
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    // Relation to multi-student
    public function sessionStudents()
    {
        return $this->hasMany(SessionStudent::class, 'session_id');
    }
}
