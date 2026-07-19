<?php
namespace App\Services;
use App\Models\Exam; use App\Models\ExamAttempt; use App\Models\User; use Illuminate\Auth\Access\AuthorizationException; use Illuminate\Support\Facades\DB; use Illuminate\Validation\ValidationException;
class ExamAccessService {
 public function assertAssigned(User $student, Exam $exam): void {
  if(!$student->isStudent()) throw new AuthorizationException('Only students may access student exams.');
  $classIds=$student->classes()->pluck('classes.id');
  $assigned=$exam->classes()->whereIn('classes.id',$classIds)->exists();
  if(!$assigned) throw new AuthorizationException('This exam is not assigned to your class.');
  if($exam->status!=='published') throw ValidationException::withMessages(['exam'=>'This exam is not published.']);
 }
 public function assertStudentCanAccess(User $student, Exam $exam): void {
  $this->assertAssigned($student,$exam);
  if(now()->lt($exam->start_at)) throw ValidationException::withMessages(['exam'=>'This exam has not started yet.']);
  if(now()->gt($exam->end_at)) throw ValidationException::withMessages(['exam'=>'This exam has already closed.']);
 }
 public function start(User $student, Exam $exam): ExamAttempt {
  return DB::transaction(function() use($student,$exam){
   $locked=Exam::query()->lockForUpdate()->findOrFail($exam->id); $this->assertStudentCanAccess($student,$locked);
   $active=ExamAttempt::where('exam_id',$locked->id)->where('student_id',$student->id)->where('status','in_progress')->latest()->first();
   if($active){return $active;}
   $count=ExamAttempt::where('exam_id',$locked->id)->where('student_id',$student->id)->count();
   if($count >= $locked->maximum_attempts) throw ValidationException::withMessages(['exam'=>'You have used all allowed attempts for this exam.']);
   $expires=now()->addMinutes($locked->duration_minutes); if($expires->gt($locked->end_at)){$expires=$locked->end_at;}
   return ExamAttempt::create(['exam_id'=>$locked->id,'student_id'=>$student->id,'attempt_number'=>$count+1,'started_at'=>now(),'expires_at'=>$expires,'status'=>'in_progress','result_status'=>'pending']);
  });
 }
}
