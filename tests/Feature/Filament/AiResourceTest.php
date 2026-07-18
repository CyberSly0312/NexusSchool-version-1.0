<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Models\AiProvider;
use App\Models\AiConversation;
use App\Models\AiKnowledgeSource;
use App\Models\AiPromptTemplate;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['status' => 'active']);
        $this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('admin');
    }

    public function test_can_render_providers_list_page(): void
    {
        $this->get(route('filament.admin.resources.ai-providers.index'))
            ->assertSuccessful();
    }

    public function test_can_render_create_provider_page(): void
    {
        $this->get(route('filament.admin.resources.ai-providers.create'))
            ->assertSuccessful();
    }

    public function test_can_render_edit_provider_page(): void
    {
        $provider = AiProvider::factory()->create(['school_id' => '1']);

        $this->get(route('filament.admin.resources.ai-providers.edit', ['record' => $provider]))
            ->assertSuccessful();
    }

    public function test_can_render_conversations_list_page(): void
    {
        $this->get(route('filament.admin.resources.ai-conversations.index'))
            ->assertSuccessful();
    }

    public function test_can_render_create_conversation_page(): void
    {
        $this->get(route('filament.admin.resources.ai-conversations.create'))
            ->assertSuccessful();
    }

    public function test_can_render_edit_conversation_page(): void
    {
        $conversation = AiConversation::factory()->create(['school_id' => '1']);

        $this->get(route('filament.admin.resources.ai-conversations.edit', ['record' => $conversation]))
            ->assertSuccessful();
    }

    public function test_can_render_knowledge_sources_list_page(): void
    {
        $this->get(route('filament.admin.resources.ai-knowledge-sources.index'))
            ->assertSuccessful();
    }

    public function test_can_render_create_knowledge_source_page(): void
    {
        $this->get(route('filament.admin.resources.ai-knowledge-sources.create'))
            ->assertSuccessful();
    }

    public function test_can_render_edit_knowledge_source_page(): void
    {
        $source = AiKnowledgeSource::factory()->create(['school_id' => '1']);

        $this->get(route('filament.admin.resources.ai-knowledge-sources.edit', ['record' => $source]))
            ->assertSuccessful();
    }

    public function test_can_render_prompt_templates_list_page(): void
    {
        $this->get(route('filament.admin.resources.ai-prompt-templates.index'))
            ->assertSuccessful();
    }

    public function test_can_render_create_prompt_template_page(): void
    {
        $this->get(route('filament.admin.resources.ai-prompt-templates.create'))
            ->assertSuccessful();
    }

    public function test_can_render_edit_prompt_template_page(): void
    {
        $template = AiPromptTemplate::factory()->create(['school_id' => '1']);

        $this->get(route('filament.admin.resources.ai-prompt-templates.edit', ['record' => $template]))
            ->assertSuccessful();
    }

    public function test_can_render_usage_logs_list_page(): void
    {
        $this->get(route('filament.admin.resources.ai-usage-logs.index'))
            ->assertSuccessful();
    }
}
