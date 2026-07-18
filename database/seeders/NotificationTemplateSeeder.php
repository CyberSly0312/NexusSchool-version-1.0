<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $templates = [
            [
                'name' => 'Fee Reminder',
                'slug' => 'fee-reminder',
                'category' => 'fee_reminder',
                'channel' => 'email',
                'subject' => 'Fee Payment Reminder - {{ student_name }}',
                'body' => 'Dear {{ parent_name }}, this is a reminder that {{ student_name }} has an outstanding fee of {{ amount }}. Please pay by {{ due_date }}.',
                'variables' => ['student_name', 'parent_name', 'amount', 'due_date'],
            ],
            [
                'name' => 'Attendance Alert',
                'slug' => 'attendance-alert',
                'category' => 'attendance_alert',
                'channel' => 'sms',
                'subject' => null,
                'body' => 'Dear {{ parent_name }}, {{ student_name }} was marked {{ status }} on {{ date }}. Reason: {{ reason }}.',
                'variables' => ['student_name', 'parent_name', 'status', 'date', 'reason'],
            ],
            [
                'name' => 'Grade Publication',
                'slug' => 'grade-publication',
                'category' => 'grade_publication',
                'channel' => 'in_app',
                'subject' => 'Results Available - {{ term_name }}',
                'body' => 'Dear {{ student_name }}, your {{ term_name }} results are now available. Average: {{ average }}.',
                'variables' => ['student_name', 'term_name', 'average'],
            ],
            [
                'name' => 'Library Overdue',
                'slug' => 'library-overdue',
                'category' => 'library_reminder',
                'channel' => 'email',
                'subject' => 'Overdue Book Reminder',
                'body' => 'Dear {{ borrower_name }}, the book "{{ book_title }}" is {{ days_overdue }} days overdue. Please return it to avoid fines.',
                'variables' => ['borrower_name', 'book_title', 'days_overdue'],
            ],
            [
                'name' => 'Transport Update',
                'slug' => 'transport-update',
                'category' => 'transport_update',
                'channel' => 'push',
                'subject' => 'Transport Update',
                'body' => 'Route {{ route_name }} has been updated. {{ update_details }}',
                'variables' => ['route_name', 'update_details'],
            ],
            [
                'name' => 'Welcome Message',
                'slug' => 'welcome-message',
                'category' => 'welcome',
                'channel' => 'email',
                'subject' => 'Welcome to {{ school_name }}',
                'body' => 'Dear {{ user_name }}, welcome to {{ school_name }}! Your account has been created successfully.',
                'variables' => ['user_name', 'school_name'],
            ],
            [
                'name' => 'Password Reset',
                'slug' => 'password-reset',
                'category' => 'password_reset',
                'channel' => 'email',
                'subject' => 'Password Reset Request',
                'body' => 'Dear {{ user_name }}, you have requested a password reset. Click the link to reset: {{ reset_url }}',
                'variables' => ['user_name', 'reset_url'],
            ],
            [
                'name' => 'Payroll Notice',
                'slug' => 'payroll-notice',
                'category' => 'payroll_notice',
                'channel' => 'email',
                'subject' => 'Salary Credit Notification',
                'body' => 'Dear {{ staff_name }}, your salary for {{ month }} has been credited. Amount: {{ amount }}.',
                'variables' => ['staff_name', 'month', 'amount'],
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::firstOrCreate(
                ['school_id' => $school->id, 'slug' => $template['slug']],
                array_merge($template, ['school_id' => $school->id])
            );
        }
    }
}
