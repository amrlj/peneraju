<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Question;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $lecturer = User::updateOrCreate(
            ['email' => 'lecturer@gmail.com'],
            [
                'name' => 'Dr. Aisyah Rahman',
                'password' => Hash::make('password'),
                'role' => 'lecturer',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );
        $student1 = User::updateOrCreate(
            ['email' => 'student1@gmail.com'],
            [
                'name' => 'Ali Student',
                'password' => Hash::make('password'),
                'role' => 'student',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );
        $student2 = User::updateOrCreate(
            ['email' => 'student2@gmail.com'],
            [
                'name' => 'Siti Student',
                'password' => Hash::make('password'),
                'role' => 'student',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );
        $student3 = User::updateOrCreate(
            ['email' => 'student3@gmail.com'],
            [
                'name' => 'Kumar Student',
                'password' => Hash::make('password'),
                'role' => 'student',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );
        $classA = SchoolClass::updateOrCreate(
            ['code' => 'CS-A'],
            [
                'name' => 'Computer Science Group A',
                'description' => 'Demo class for the online examination portal.',
                'lecturer_id' => $lecturer->id,
                'academic_year' =>
                '2026/2027',
                'is_active' => true
            ]
        );
        $classB = SchoolClass::updateOrCreate(
            ['code' => 'CS-B'],
            [
                'name' => 'Computer Science Group B',
                'description' => 'Second demo class.',
                'lecturer_id' => $lecturer->id,
                'academic_year' => '2026/2027',
                'is_active' => true
            ]
        );
        $classA->students()->sync([$student1->id, $student2->id]);
        $classB->students()->sync([$student3->id]);

        $web = Subject::updateOrCreate(
            ['code' => 'WEB101'],
            ['name' => 'Web Programming', 'description' => 'Laravel and web development fundamentals.', 'created_by' => $lecturer->id, 'is_active' => true]
        );
        $db = Subject::updateOrCreate(
            ['code' => 'DB101'],
            ['name' => 'Database Systems', 'description' => 'Relational database concepts.', 'created_by' => $lecturer->id, 'is_active' => true]
        );
        $web->classes()->sync(
            [
                $classA->id => ['lecturer_id' => $lecturer->id],
                $classB->id => ['lecturer_id' => $lecturer->id]
            ]
        );
        $db->classes()->sync(
            [$classA->id => ['lecturer_id' => $lecturer->id]]
        );
        $q1 = Question::updateOrCreate(
            [
                'subject_id' => $web->id,
                'question_text' => 'Which Artisan command creates a new Laravel controller?'
            ],
            [
                'created_by' => $lecturer->id,
                'question_type' => 'multiple_choice',
                'marks' => 2,
                'is_active' => true
            ]
        );
        $q1->options()->delete();
        foreach ([['php artisan make:model', 0], ['php artisan make:controller', 1], ['php artisan create:controller', 0], ['php artisan controller:new', 0]] as $i => $o) $q1->options()->create(['option_text' => $o[0], 'is_correct' => $o[1], 'sort_order' => $i + 1]);
        $q2 = Question::updateOrCreate(
            [
                'subject_id' => $web->id,
                'question_text' => 'What is the main purpose of Laravel middleware?'
            ],
            [
                'created_by' => $lecturer->id,
                'question_type' => 'multiple_choice',
                'marks' => 2,
                'is_active' => true
            ]
        );
        $q2->options()->delete();
        foreach ([['To define database columns', 0], ['To filter or inspect HTTP requests', 1], ['To compile CSS files', 0], ['To create Blade components', 0]] as $i => $o) $q2->options()->create(['option_text' => $o[0], 'is_correct' => $o[1], 'sort_order' => $i + 1]);
        $q3 = Question::updateOrCreate(
            [
                'subject_id' => $web->id,
                'question_text' => 'Explain how server-side validation improves the security of an online examination system.'
            ],
            [
                'created_by' => $lecturer->id,
                'question_type' => 'open_text',
                'marks' => 6,
                'is_active' => true
            ]
        );
        $q4 = Question::updateOrCreate(
            [
                'subject_id' => $db->id,
                'question_text' => 'Which SQL clause filters rows before grouping?'
            ],
            [
                'created_by' => $lecturer->id,
                'question_type' => 'multiple_choice',
                'marks' => 2,
                'is_active' => true
            ]
        );
        $q4->options()->delete();
        foreach ([['ORDER BY', 0], ['HAVING', 0], ['WHERE', 1], ['GROUP BY', 0]] as $i => $o) $q4->options()->create(['option_text' => $o[0], 'is_correct' => $o[1], 'sort_order' => $i + 1]);
        $exam = Exam::updateOrCreate(
            ['title' => 'Laravel Fundamentals Assessment', 'subject_id' => $web->id],
            ['created_by' => $lecturer->id, 'instructions' => 'Answer all questions. MCQ answers are marked automatically. The written answer will be reviewed by the lecturer.', 'start_at' => now()->subMinutes(10), 'end_at' => now()->addDay(), 'duration_minutes' => 15, 'passing_percentage' => 50, 'maximum_attempts' => 1, 'status' => 'published', 'show_result' => true, 'show_correct_answers' => true, 'randomize_questions' => false, 'randomize_options' => true]
        );
        $exam->classes()->sync([$classA->id, $classB->id]);
        $exam->questions()->sync([$q1->id => ['marks' => 2, 'sort_order' => 1], $q2->id => ['marks' => 2, 'sort_order' => 2], $q3->id => ['marks' => 6, 'sort_order' => 3]]);
        $draft = Exam::updateOrCreate(
            ['title' => 'Database Practice Quiz', 'subject_id' => $db->id],
            ['created_by' => $lecturer->id, 'instructions' => 'Draft demonstration exam.', 'start_at' => now()->addDays(2), 'end_at' => now()->addDays(3), 'duration_minutes' => 20, 'passing_percentage' => 50, 'maximum_attempts' => 1, 'status' => 'draft', 'show_result' => true, 'show_correct_answers' => false, 'randomize_questions' => false, 'randomize_options' => false]
        );
        $draft->classes()->sync(
            [$classA->id]
        );
        $draft->questions()->sync(
            [$q4->id => [
                'marks' => 2,
                'sort_order' => 1
            ]]
        );
    }
}
