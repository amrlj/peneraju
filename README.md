# Online Examination and Student Management Portal

A complete Laravel 11 portal for lecturers and students, built with Laravel Breeze Blade authentication, Tailwind CSS, Eloquent ORM, server-side exam timing, automatic MCQ marking and manual open-text marking.

## Requirements Covered

- Two roles: **Lecturer** and **Student**.
- Secure credential-based authentication using Laravel Breeze scaffolding.
- Lecturer question bank with **multiple-choice** and **open-text** questions.
- Class and subject management.
- Many-to-many assignment of students to classes and subjects to classes.
- Exams assigned to one or more classes.
- Students can only access exams assigned to their classes.
- Server-authoritative exam time limits and automatic submission.
- Automatic MCQ marking and lecturer marking/feedback for open-text answers.
- Result display, attempt tracking, audit logs and CSV export.

## Technology

- PHP 8.2+
- Laravel 11
- Laravel Breeze (Blade)
- Tailwind CSS and Alpine.js
- MySAQL
- PHPUnit 11

## Installation

```bash
git clone https://github.com/amrlj/peneraju.git
cd peneraju
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Create the SQLite database
```

Run the migrations and demo seeder:

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

Open `http://127.0.0.1:8000`.

### MySQL option

Replace the SQLite configuration in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=peneraju
DB_USERNAME=root
DB_PASSWORD=
```

Then run `php artisan migrate --seed`.

## Demo Accounts

| Role | Email | Password |
|---|---|---|
| Lecturer | `lecturer@gmail.com` | `password` |
| Student | `student1@gmail.com` | `password` |
| Student | `student2@gmail.com` | `password` |
| Student | `student3@gmail.com` | `password` |

Change these passwords before any real deployment.

## Main Workflows

### Lecturer

1. Log in and create classes.
2. Assign student accounts to classes.
3. Create subjects and associate them with classes.
4. Add MCQ or open-text questions to the question bank.
5. Create an exam, choose a subject, assigned classes and questions.
6. Configure start/end date, duration, passing mark and attempts.
7. Publish the exam.
8. Review attempts, mark open-text answers, give feedback and export results.

### Student

1. Log in and view exams assigned to the student's class.
2. Open an available exam and start an attempt.
3. Answer questions; answers autosave to the server.
4. Submit manually or allow automatic submission when time expires.
5. View released results and lecturer feedback.

## Security and Access Control

- `role` and `active` middleware protect portal sections.
- Lecturer controllers scope records to the authenticated lecturer.
- Student exam access is checked against class membership on the server.
- Exam availability, maximum attempts and expiry are validated server-side.
- Correct MCQ flags are never rendered on the examination page.
- Submitted attempts are locked against further answer changes.
- CSRF protection is applied to all forms and autosave requests.
- Login attempts are rate-limited by Breeze's login request.

## Database Structure

Core tables:

- `users`
- `classes`, `class_student`
- `subjects`, `class_subject`
- `questions`, `question_options`
- `exams`, `exam_class`, `exam_questions`
- `exam_attempts`, `student_answers`

## Testing

```bash
php artisan test
```
## Useful Commands

```bash
php artisan migrate:fresh --seed
php artisan route:list
npm run dev
php artisan test
```