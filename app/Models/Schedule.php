<?php

namespace App\Models;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_recurring' => 'boolean',
    ];

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($schedule) {
            if ($schedule->is_recurring && $schedule->recurrence_frequency) {
                $schedule->createRecurringSchedules();
            }
        });
    }

    public function createRecurringSchedules()
    {
        $startDate = Carbon::parse($this->scheduled_at);
        $endDate = $startDate->copy()->addMonths(3); // Criar recorrências para os próximos 3 meses
        $nextDate = $startDate->copy();
    
        while ($nextDate->lessThanOrEqualTo($endDate)) {
            $nextDate = match ($this->recurrence_frequency) {
                'daily' => $nextDate->copy()->addDay(),
                'weekly' => $nextDate->copy()->addWeek(),
                'monthly' => $nextDate->copy()->addMonthNoOverflow(),
                default => null,
            };
    
            if ($nextDate && $nextDate->lessThanOrEqualTo($endDate)) {
                Schedule::firstOrCreate([
                    'professor_id' => $this->professor_id,
                    'student_id' => $this->student_id,
                    'scheduled_at' => $nextDate,
                    'is_recurring' => false, // Evita loops infinitos
                    'recurrence_frequency' => null,
                ]);
            }
        }
    }
    
    
}
    