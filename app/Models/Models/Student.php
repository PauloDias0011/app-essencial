<?php

namespace App\Models\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date_of_birth',
        'grade_year',
        'special_observations',
        'gender',
        'school',
        'address',
        'parent_id',
        'professor_id',
    ];

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function classPlans()
    {
        return $this->hasMany(ClassPlan::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
