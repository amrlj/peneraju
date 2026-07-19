<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_active' => 'boolean'];
    }
    public function isLecturer(): bool
    {
        return $this->role === 'lecturer';
    }
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_student', 'student_id', 'class_id')->withPivot('enrolled_at')->withTimestamps();
    }
    public function lecturerClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'lecturer_id');
    }
    public function createdSubjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'created_by');
    }
    public function createdQuestions(): HasMany
    {
        return $this->hasMany(Question::class, 'created_by');
    }
    public function createdExams(): HasMany
    {
        return $this->hasMany(Exam::class, 'created_by');
    }
    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class, 'student_id');
    }
}
