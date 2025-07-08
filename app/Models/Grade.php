<?php

namespace App\Models;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'professor_id',
        'subject',
        'month',     // ✅ Mudou de 'semester' para 'month'
        'grade',
        'year',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    // ✅ Helper para obter nome do mês
    public function getMonthNameAttribute()
    {
        $months = [
            1 => 'Janeiro',
            2 => 'Fevereiro', 
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro',
        ];

        return $months[$this->month] ?? '';
    }

    // ✅ Scope para filtrar por período
    public function scopeByPeriod($query, $month = null, $year = null)
    {
        if ($month) {
            $query->where('month', $month);
        }
        
        if ($year) {
            $query->where('year', $year);
        }

        return $query;
    }
}