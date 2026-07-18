<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'code', 'description', 'created_by', 'is_active'];
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subject', 'subject_id', 'class_id')->withPivot('lecturer_id')->withTimestamps();
    }
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}
