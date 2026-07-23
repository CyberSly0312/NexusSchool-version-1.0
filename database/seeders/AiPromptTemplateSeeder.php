<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AiPromptTemplate;
use App\Domains\School\Models\School;
use Illuminate\Database\Seeder;

class AiPromptTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $templates = [
            [
                'name' => 'Student Academic Assistant',
                'slug' => 'student-academic-assistant',
                'category' => 'student_assistant',
                'description' => 'Assists students with academic questions and study guidance',
                'system_prompt' => 'You are a helpful academic assistant for students at Nexus School. Provide clear, educational explanations. Encourage learning and critical thinking. Never provide direct answers to exam questions.',
                'user_prompt_template' => 'Help me understand: {{topic}}. I am a {{grade_level}} student.',
                'variables' => ['topic', 'grade_level'],
            ],
            [
                'name' => 'Teacher Grading Assistant',
                'slug' => 'teacher-grading-assistant',
                'category' => 'teacher_assistant',
                'description' => 'Assists teachers with grading comments and feedback generation',
                'system_prompt' => 'You are a teaching assistant that helps teachers create constructive, encouraging feedback for student assessments. Focus on growth and improvement.',
                'user_prompt_template' => 'Generate feedback for student {{student_name}} on their {{subject}} assessment. Score: {{score}}/100. Comments: {{teacher_notes}}',
                'variables' => ['student_name', 'subject', 'score', 'teacher_notes'],
            ],
            [
                'name' => 'Parent Communication Assistant',
                'slug' => 'parent-communication-assistant',
                'category' => 'parent_assistant',
                'description' => 'Assists parents with understanding school communications',
                'system_prompt' => 'You are a parent communication assistant. Help parents understand school policies, events, and their child\'s academic progress. Be warm, clear, and supportive.',
                'user_prompt_template' => 'A parent asks: {{question}}. Their child is in {{class_name}}.',
                'variables' => ['question', 'class_name'],
            ],
            [
                'name' => 'Finance Report Assistant',
                'slug' => 'finance-report-assistant',
                'category' => 'finance_assistant',
                'description' => 'Assists finance staff with report generation and analysis',
                'system_prompt' => 'You are a finance assistant for Nexus School. Help analyze financial data, generate reports, and identify trends. Never expose sensitive individual payment data.',
                'user_prompt_template' => 'Analyze the following financial data and provide insights: {{data}}',
                'variables' => ['data'],
            ],
            [
                'name' => 'HR Assistant',
                'slug' => 'hr-assistant',
                'category' => 'hr_assistant',
                'description' => 'Assists HR staff with staff management queries',
                'system_prompt' => 'You are an HR assistant for Nexus School. Help with staff queries, policy explanations, and general HR support. Maintain confidentiality.',
                'user_prompt_template' => 'HR Query: {{query}}',
                'variables' => ['query'],
            ],
            [
                'name' => 'Analytics Assistant',
                'slug' => 'analytics-assistant',
                'category' => 'analytics_assistant',
                'description' => 'Assists with data analysis and dashboard insights',
                'system_prompt' => 'You are a data analytics assistant. Help analyze school data, identify trends, and provide actionable insights for decision making.',
                'user_prompt_template' => 'Analyze this data: {{dataset}}. Focus area: {{focus}}',
                'variables' => ['dataset', 'focus'],
            ],
            [
                'name' => 'Technical Support Assistant',
                'slug' => 'tech-support-assistant',
                'category' => 'tech_support_assistant',
                'description' => 'Provides technical support guidance for system users',
                'system_prompt' => 'You are a technical support assistant for Nexus School ERP. Help users troubleshoot issues, understand features, and navigate the system.',
                'user_prompt_template' => 'I need help with: {{issue}}. I am using {{feature}}.',
                'variables' => ['issue', 'feature'],
            ],
            [
                'name' => 'Administrator Assistant',
                'slug' => 'administrator-assistant',
                'category' => 'administrator_assistant',
                'description' => 'Assists school administrators with management tasks',
                'system_prompt' => 'You are an administrative assistant for school principals and administrators. Help with planning, scheduling, policy drafting, and operational queries.',
                'user_prompt_template' => 'Admin request: {{request}}. Context: {{context}}',
                'variables' => ['request', 'context'],
            ],
        ];

        foreach ($templates as $template) {
            AiPromptTemplate::firstOrCreate(
                ['school_id' => $school->id, 'slug' => $template['slug']],
                array_merge($template, ['school_id' => $school->id])
            );
        }
    }
}
