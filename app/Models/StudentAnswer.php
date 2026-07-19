<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAnswer extends Model
{
    use HasFactory;
    protected $fillable = ['exam_attempt_id', 'question_id', 'question_option_id', 'answer_text', 'is_correct', 'marks_awarded', 'lecturer_feedback', 'marked_by', 'marked_at'];
    protected function casts(): array
    {
        return ['is_correct' => 'boolean', 'marks_awarded' => 'decimal:2', 'marked_at' => 'datetime'];
    }
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id');
    }
    public function marker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
