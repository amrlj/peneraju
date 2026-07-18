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
            ['email' => 'lecturer@example.com'],
            [
                'name' => 'Dr. Aisyah Rahman',
                'password' => Hash::make('password'),
                'role' => 'lecturer',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );
        $student1 = User::updateOrCreate(
            ['email' => 'student1@example.com'],
            [
                'name' => 'Ali Student',
                'password' => Hash::make('password'),
                'role' => 'student',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );
        $student2 = User::updateOrCreate(
            ['email' => 'student2@example.com'],
            [
                'name' => 'Siti Student',
                'password' => Hash::make('password'),
                'role' => 'student',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );
        $student3 = User::updateOrCreate(
            ['email' => 'student3@example.com'],
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
    }
}
