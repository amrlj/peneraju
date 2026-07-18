@extends('layouts.app')
@section('title', $question->exists ? 'Edit Question' : 'Create Question')
@section('heading', $question->exists ? 'Edit Question' : 'Create Question')
@section('content')
    @php($initialOptions = old('options', $question->exists ? $question->options->map(fn($o) => ['option_text' => $o->option_text, 'is_correct' => $o->is_correct])->values()->all() : [['option_text' => '', 'is_correct' => true], ['option_text' => '', 'is_correct' => false], ['option_text' => '', 'is_correct' => false], ['option_text' => '', 'is_correct' => false]]))
    <form method="POST"
        action="{{ $question->exists ? route('lecturer.questions.update', $question) : route('lecturer.questions.store') }}"
        class="space-y-6" x-data='{ type: @json(old('question_type', $question->question_type ?: 'multiple_choice')), options: @json($initialOptions) }'>
        @csrf @if ($question->exists)
            @method('PUT')
        @endif
        <div class="card space-y-5">
            <div class="grid gap-5 md:grid-cols-3">
                <div><label class="label">Subject</label><select class="form-input" name="subject_id" required>
                        <option value="">Select subject</option>
                        @foreach ($subjects as $s)
                            <option value="{{ $s->id }}" @selected(old('subject_id', $question->subject_id) == $s->id)>{{ $s->code }} —
                                {{ $s->name }}</option>
                        @endforeach
                    </select></div>
                <div><label class="label">Question type</label><select class="form-input" name="question_type"
                        x-model="type">
                        <option value="multiple_choice">Multiple choice</option>
                        <option value="open_text">Open text</option>
                    </select></div>
                <div><label class="label">Default marks</label><input class="form-input" type="number" step="0.5"
                        min="0.5" name="marks" value="{{ old('marks', $question->marks ?: 1) }}" required></div>
            </div>
            <div><label class="label">Question</label>
                <textarea class="form-input" name="question_text" rows="4" required>{{ old('question_text', $question->question_text) }}</textarea>
            </div>
            <div><input type="hidden" name="is_active" value="0"><label class="flex items-center gap-2"><input
                        class="rounded" type="checkbox" name="is_active" value="1" @checked(old('is_active', $question->exists ? $question->is_active : true))> Active
                    question</label></div>
        </div>
        <div class="card" x-show="type==='multiple_choice'">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold">Answer options</h2>
                    <p class="text-sm text-slate-500">Tick exactly one correct answer.</p>
                </div><button type="button" class="btn-secondary"
                    @click="options.push({option_text:'',is_correct:false})">Add Option</button>
            </div>
            <div class="mt-4 space-y-3"><template x-for="(option,index) in options" :key="index">
                    <div class="flex items-center gap-3"><input type="hidden" :name="`options[${index}][is_correct]`"
                            value="0"><input class="rounded" type="checkbox" :name="`options[${index}][is_correct]`"
                            value="1" x-model="option.is_correct"><input class="form-input mt-0"
                            :name="`options[${index}][option_text]`" x-model="option.option_text"
                            placeholder="Option text"><button type="button" class="text-sm text-red-600"
                            @click="options.splice(index,1)" x-show="options.length>2">Remove</button></div>
                </template></div>
        </div>
        <div class="flex gap-3"><button class="btn-primary">Save Question</button><a class="btn-secondary"
                href="{{ route('lecturer.questions.index') }}">Cancel</a></div>
    </form>
@endsection
