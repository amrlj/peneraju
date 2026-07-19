<?php
namespace App\Services;
use App\Models\ExamAttempt; use App\Models\StudentAnswer; use App\Models\User; use Illuminate\Support\Facades\DB; use Illuminate\Validation\ValidationException;
class ExamScoringService {
 public function submit(ExamAttempt $attempt, bool $automatic=false): ExamAttempt {
  return DB::transaction(function() use($attempt,$automatic){
   $attempt=ExamAttempt::query()->lockForUpdate()->with(['exam.questions.options','answers'])->findOrFail($attempt->id);
   if($attempt->status!=='in_progress') return $attempt;
   $objective=0.0; $hasOpenText=false;
   foreach($attempt->exam->questions as $question){
    $answer=$attempt->answers->firstWhere('question_id',$question->id);
    if($question->isMultipleChoice()){
     $correct=$question->options->firstWhere('is_correct',true);
     $isCorrect=$answer && $correct && (int)$answer->question_option_id===(int)$correct->id;
     $marks=$isCorrect ? (float)$question->pivot->marks : 0.0; $objective += $marks;
     if($answer){$answer->update(['is_correct'=>$isCorrect,'marks_awarded'=>$marks]);}
    } else {
     $hasOpenText=true;
     if(!$answer){StudentAnswer::create(['exam_attempt_id'=>$attempt->id,'question_id'=>$question->id,'answer_text'=>null]);}
    }
   }
   $totalMarks=(float)$attempt->exam->questions->sum(fn($q)=>(float)$q->pivot->marks);
   $percentage=$totalMarks>0 ? round(($objective/$totalMarks)*100,2) : 0;
   $attempt->update(['submitted_at'=>now(),'status'=>$automatic?'auto_submitted':'submitted','objective_score'=>$objective,'subjective_score'=>0,'total_score'=>$objective,'percentage'=>$percentage,'result_status'=>$hasOpenText?'pending':($percentage >= (float)$attempt->exam->passing_percentage?'passed':'failed')]);
   return $attempt->fresh(['exam','answers']);
  });
 }
 public function markOpenText(ExamAttempt $attempt, User $lecturer, array $marks): ExamAttempt {
  return DB::transaction(function() use($attempt,$lecturer,$marks){
   $attempt=ExamAttempt::query()->lockForUpdate()->with(['exam.questions','answers'])->findOrFail($attempt->id);
   if($attempt->exam->created_by!==$lecturer->id) throw ValidationException::withMessages(['attempt'=>'You cannot mark this exam.']);
   if($attempt->status==='in_progress') throw ValidationException::withMessages(['attempt'=>'An active attempt cannot be marked.']);
   foreach($attempt->exam->questions->where('question_type','open_text') as $question){
    $data=$marks[$question->id] ?? null; if(!$data) continue;
    $max=(float)$question->pivot->marks; $awarded=max(0,min($max,(float)($data['marks_awarded'] ?? 0)));
    StudentAnswer::updateOrCreate(['exam_attempt_id'=>$attempt->id,'question_id'=>$question->id],['marks_awarded'=>$awarded,'lecturer_feedback'=>$data['lecturer_feedback'] ?? null,'marked_by'=>$lecturer->id,'marked_at'=>now()]);
   }
   $attempt->load(['exam.questions','answers']);
   $objective=(float)$attempt->answers->whereNotNull('is_correct')->sum('marks_awarded');
   $subjective=(float)$attempt->answers->whereNull('is_correct')->sum('marks_awarded');
   $totalMarks=(float)$attempt->exam->questions->sum(fn($q)=>(float)$q->pivot->marks); $total=$objective+$subjective;
   $percentage=$totalMarks>0 ? round(($total/$totalMarks)*100,2) : 0;
   $openIds=$attempt->exam->questions->where('question_type','open_text')->pluck('id');
   $markedCount=$attempt->answers->whereIn('question_id',$openIds)->whereNotNull('marked_at')->count();
   $allMarked=$markedCount===$openIds->count();
   $attempt->update(['objective_score'=>$objective,'subjective_score'=>$subjective,'total_score'=>$total,'percentage'=>$percentage,'status'=>$allMarked?'marked':$attempt->status,'result_status'=>$allMarked?($percentage >= (float)$attempt->exam->passing_percentage?'passed':'failed'):'pending']);
   return $attempt->fresh(['answers']);
  });
 }
}
