<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;
    protected $fillable = ['subject_id', 'created_by', 'title', 'instructions', 'start_at', 'end_at', 'duration_minutes', 'passing_percentage', 'maximum_attempts', 'status', 'show_result', 'show_correct_answers', 'randomize_questions', 'randomize_options'];
    protected function casts(): array
    {
        return ['start_at' => 'datetime', 'end_at' => 'datetime', 'passing_percentage' => 'decimal:2', 'show_result' => 'boolean', 'show_correct_answers' => 'boolean', 'randomize_questions' => 'boolean', 'randomize_options' => 'boolean'];
    }
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'exam_class', 'exam_id', 'class_id')->withTimestamps();
    }
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'exam_questions')->withPivot(['marks', 'sort_order'])->withTimestamps()->orderBy('exam_questions.sort_order');
    }
    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }
    public function totalMarks(): float
    {
        return (float)$this->questions()->sum('exam_questions.marks');
    }
    public function isOpen(): bool
    {
        return $this->status === 'published' && now()->between($this->start_at, $this->end_at);
    }
}
