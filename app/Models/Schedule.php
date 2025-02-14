<?php

namespace App\Models;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'professor_id',
        'student_id',
        'scheduled_at',
        'is_recurring',
        'recurrence_frequency',
    ];

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function expandRecurrence()
    {
        if ($this->is_recurring && $this->recurrence_frequency) {
            $nextDate = $this->scheduled_at;

            for ($i = 0; $i < 5; $i++) { // Exemplo: criar 5 instâncias de agendamentos
                if ($this->recurrence_frequency === 'daily') {
                    $nextDate->addDay();
                } elseif ($this->recurrence_frequency === 'weekly') {
                    $nextDate->addWeek();
                } elseif ($this->recurrence_frequency === 'monthly') {
                    $nextDate->addMonth();
                }

                Schedule::create([
                    'professor_id' => $this->professor_id,
                    'student_id' => $this->student_id,
                    'scheduled_at' => $nextDate,
                    'is_recurring' => false, // Instâncias não são recorrentes
                ]);
            }
        }
    }
}
