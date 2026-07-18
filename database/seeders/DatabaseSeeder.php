<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::updateOrCreate(['email' => 'aisyah@example.com'], ['name' => 'Aisyah Rahman', 'password' => Hash::make('password'), 'role' => 'lecturer', 'is_active' => true, 'email_verified_at' => now()]);
        User::updateOrCreate(['email' => 'Ali@example.com'], ['name' => 'Ali', 'password' => Hash::make('password'), 'role' => 'student', 'is_active' => true, 'email_verified_at' => now()]);
        User::updateOrCreate(['email' => 'Siti@example.com'], ['name' => 'Siti', 'password' => Hash::make('password'), 'role' => 'student', 'is_active' => true, 'email_verified_at' => now()]);
        User::updateOrCreate(['email' => 'Kumar@example.com'], ['name' => 'Kumar', 'password' => Hash::make('password'), 'role' => 'student', 'is_active' => true, 'email_verified_at' => now()]);
    }
}
