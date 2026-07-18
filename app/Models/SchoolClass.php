<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolClass extends Model
{
    use HasFactory;
    protected $table = 'classes';
    protected $fillable = ['name', 'code', 'description', 'lecturer_id', 'academic_year', 'is_active'];
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_student', 'class_id', 'student_id')->withPivot('enrolled_at')->withTimestamps();
    }
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_id', 'subject_id')->withPivot('lecturer_id')->withTimestamps();
    }
    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_class', 'class_id', 'exam_id')->withTimestamps();
    }
}
