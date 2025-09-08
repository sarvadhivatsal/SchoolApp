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
    ];
    protected $casts = [
        'goal_ids' => 'array', // automatically cast JSON to array
    ];

    // Relation to Teacher
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

    // Relation to Student
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
    //     public function studentGoals()
    // {
    //     return $this->hasMany(\App\Models\StudentGoal::class, 'session_id');
    // }
    // public function goals()
    // {
    //     return $this->belongsToMany(StudentGoal::class, 'student_goals', 'id', 'id')
    //                 ->whereIn('id', $this->goal_ids ?? []);
    // }

}
