<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $table = 'payroll'; 

    protected $fillable = [
        'teacher_name',
        'student_name',
        'month',
        'year',
        'session_duration',
        'session_rate',
    ];

    // Optional: Accessor to format month/year nicely
    public function getMonthYearAttribute()
    {
        return date('F Y', mktime(0, 0, 0, $this->month, 1, $this->year));
    }
}
