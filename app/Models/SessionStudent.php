<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionStudent extends Model
{
    use HasFactory;

    protected $table = 'session_students';

    protected $fillable = [
        'session_id',
        'student_ids',
    ];

    // Parent session
    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    // Student relation
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_ids', 'id');
    }
}
