<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiPromptTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AiPromptTemplateFactory extends Factory
{
    protected $model = AiPromptTemplate::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'school_id' => '1',
            'name' => ucfirst($name),
            'category' => 'student_assistant',
            'description' => fake()->sentence(),
            'system_prompt' => 'You are a helpful school assistant.',
            'user_prompt_template' => 'Help the student with: {{question}}',
            'variables' => ['question'],
            'status' => 'active',
            'version' => 1,
            'target_model' => 'gpt-4o',
            'temperature' => 0.7,
            'max_tokens' => 4096,
            'top_p' => null,
            'metadata' => null,
            'created_by' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => ['school_id' => $schoolId]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 'inactive']);
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft']);
    }

    public function studentAssistant(): static
    {
        return $this->state(fn () => ['category' => 'student_assistant']);
    }

    public function teacherAssistant(): static
    {
        return $this->state(fn () => ['category' => 'teacher_assistant']);
    }

    public function financeAssistant(): static
    {
        return $this->state(fn () => ['category' => 'finance_assistant']);
    }
}
