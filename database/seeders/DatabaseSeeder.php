<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with essential users only.
     * All courses, lessons, and exams data will be created by teachers and students.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin Iglal',
            'email' => 'admin@iglal.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        // Create Teacher User
        $teacher = User::create([
            'name' => 'Teacher Anglal',
            'email' => 'teacher@iglal.com',
            'password' => bcrypt('password'),
            'role' => 'teacher'
        ]);

        // Create Student Users
        $student1 = User::create([
            'name' => 'Student Ahmed',
            'email' => 'student@iglal.com',
            'password' => bcrypt('password'),
            'role' => 'student'
        ]);

        $student2 = User::create([
            'name' => 'Student Fatima',
            'email' => 'fatima@iglal.com',
            'password' => bcrypt('password'),
            'role' => 'student'
        ]);
    }
}
