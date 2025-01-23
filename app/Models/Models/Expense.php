<?php

namespace App\Models\Models;

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
    ];

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }
}
