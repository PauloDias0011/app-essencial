<?php

namespace App\Models\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'professor_id',
        'file_path',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }
}
