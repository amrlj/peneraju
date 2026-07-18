<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;
    protected $fillable = ['subject_id', 'created_by', 'question_text', 'question_type', 'marks', 'is_active'];
    protected function casts(): array
    {
        return ['marks' => 'decimal:2', 'is_active' => 'boolean'];
    }
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('sort_order');
    }
    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_questions')->withPivot(['marks', 'sort_order'])->withTimestamps();
    }
    public function isMultipleChoice(): bool
    {
        return $this->question_type === 'multiple_choice';
    }
    public function isOpenText(): bool
    {
        return $this->question_type === 'open_text';
    }
}
