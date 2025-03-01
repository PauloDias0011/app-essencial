<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'details',
        'professor_id',
        'total_cost',
        'is_reimbursed',
        'date_expense'
    ];

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }
}
