<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'first_name','last_name','status','parent_email','parent_phone',
        'dob','gender','address','city','state','zipcode','teacher_id'
    ];

    public function teacher() {
        return $this->belongsTo(Teacher::class);
    }
     public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
   
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
    public function goals()
{
    return $this->hasMany(\App\Models\StudentGoal::class);
}

}
