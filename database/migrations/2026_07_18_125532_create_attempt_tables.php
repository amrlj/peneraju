<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('exam_attempts', function(Blueprint $t){
   $t->id(); $t->foreignId('exam_id')->constrained()->restrictOnDelete(); $t->foreignId('student_id')->constrained('users')->restrictOnDelete();
   $t->unsignedInteger('attempt_number')->default(1); $t->dateTime('started_at'); $t->dateTime('expires_at'); $t->dateTime('submitted_at')->nullable();
   $t->string('status')->default('in_progress')->index(); $t->decimal('objective_score',8,2)->default(0); $t->decimal('subjective_score',8,2)->default(0);
   $t->decimal('total_score',8,2)->default(0); $t->decimal('percentage',5,2)->default(0); $t->string('result_status')->default('pending'); $t->timestamps();
   $t->unique(['exam_id','student_id','attempt_number']);
  });
  Schema::create('student_answers', function(Blueprint $t){
   $t->id(); $t->foreignId('exam_attempt_id')->constrained()->cascadeOnDelete(); $t->foreignId('question_id')->constrained()->restrictOnDelete();
   $t->foreignId('question_option_id')->nullable()->constrained('question_options')->nullOnDelete(); $t->longText('answer_text')->nullable();
   $t->boolean('is_correct')->nullable(); $t->decimal('marks_awarded',8,2)->nullable(); $t->text('lecturer_feedback')->nullable();
   $t->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete(); $t->dateTime('marked_at')->nullable(); $t->timestamps();
   $t->unique(['exam_attempt_id','question_id']);
  });
  Schema::create('activity_logs', function(Blueprint $t){
   $t->id(); $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); $t->string('action'); $t->nullableMorphs('entity');
   $t->text('description')->nullable(); $t->string('ip_address',45)->nullable(); $t->text('user_agent')->nullable(); $t->timestamps();
  });
 }
 public function down(): void {Schema::dropIfExists('activity_logs');Schema::dropIfExists('student_answers');Schema::dropIfExists('exam_attempts');}
};
