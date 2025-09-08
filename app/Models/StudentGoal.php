<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGoal extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'short_term_goal',
        'long_term_goal',
        'session_id',
    ];
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
}
