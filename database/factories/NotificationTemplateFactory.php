<?php

namespace Database\Factories;

use App\Models\NotificationTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<NotificationTemplate>
 */
class NotificationTemplateFactory extends Factory
{
    protected $model = NotificationTemplate::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);

        return [
            'school_id' => '1',
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'category' => $this->faker->randomElement([
                'fee_reminder', 'attendance_alert', 'grade_publication',
                'library_reminder', 'transport_update', 'payroll_notice',
                'password_reset', 'welcome', 'general',
            ]),
            'channel' => $this->faker->randomElement(['email', 'sms', 'whatsapp', 'push', 'in_app']),
            'subject' => $this->faker->sentence(4),
            'body' => $this->faker->paragraph(2),
            'variables' => ['student_name', 'date', 'amount'],
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }

    public function feeReminder(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'fee_reminder',
            'name' => 'Fee Reminder',
            'slug' => 'fee-reminder',
            'subject' => 'Fee Payment Reminder',
            'body' => 'Dear {{ student_name }}, please pay your fee of {{ amount }} by {{ date }}.',
            'variables' => ['student_name', 'amount', 'date'],
        ]);
    }

    public function attendanceAlert(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'attendance_alert',
            'name' => 'Attendance Alert',
            'slug' => 'attendance-alert',
            'subject' => 'Attendance Notification',
            'body' => 'Dear {{ parent_name }}, {{ student_name }} was absent on {{ date }}.',
            'variables' => ['student_name', 'parent_name', 'date'],
        ]);
    }
}
