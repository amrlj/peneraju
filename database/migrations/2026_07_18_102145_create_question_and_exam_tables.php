<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $t->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $t->text('question_text');
            $t->string('question_type');
            $t->decimal('marks', 8, 2)->default(1);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->index(['subject_id', 'question_type']);
        });
        Schema::create('question_options', function (Blueprint $t) {
            $t->id();
            $t->foreignId('question_id')->constrained()->cascadeOnDelete();
            $t->string('option_text');
            $t->boolean('is_correct')->default(false);
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });
        Schema::create('exams', function (Blueprint $t) {
            $t->id();
            $t->foreignId('subject_id')->constrained()->restrictOnDelete();
            $t->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $t->string('title');
            $t->text('instructions')->nullable();
            $t->dateTime('start_at');
            $t->dateTime('end_at');
            $t->unsignedInteger('duration_minutes');
            $t->decimal('passing_percentage', 5, 2)->default(50);
            $t->unsignedInteger('maximum_attempts')->default(1);
            $t->string('status')->default('draft')->index();
            $t->boolean('show_result')->default(true);
            $t->boolean('show_correct_answers')->default(false);
            $t->boolean('randomize_questions')->default(false);
            $t->boolean('randomize_options')->default(false);
            $t->timestamps();
        });
        Schema::create('exam_class', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $t->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $t->timestamps();
            $t->unique(['exam_id', 'class_id']);
        });
        Schema::create('exam_questions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $t->foreignId('question_id')->constrained()->restrictOnDelete();
            $t->decimal('marks', 8, 2);
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
            $t->unique(['exam_id', 'question_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exam_class');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
    }
};
