<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'account_id','dob','phone','gender','address','city','state','zipcode'
    ];

    public function account() {
        return $this->belongsTo(Account::class,'account_id');
    }

    public function students() {
        return $this->hasMany(Student::class);
    }
     public function sessions()
    {
        return $this->hasMany(Session::class);
    }
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
