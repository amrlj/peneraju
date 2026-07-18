<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    use HasFactory;
    protected $fillable = ['exam_id', 'student_id', 'attempt_number', 'started_at', 'expires_at', 'submitted_at', 'status', 'objective_score', 'subjective_score', 'total_score', 'percentage', 'result_status'];
    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'expires_at' => 'datetime', 'submitted_at' => 'datetime', 'objective_score' => 'decimal:2', 'subjective_score' => 'decimal:2', 'total_score' => 'decimal:2', 'percentage' => 'decimal:2'];
    }
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
    public function answers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }
    public function isActive(): bool
    {
        return $this->status === 'in_progress' && now()->lte($this->expires_at);
    }
    public function isSubmitted(): bool
    {
        return $this->status !== 'in_progress';
    }
}
