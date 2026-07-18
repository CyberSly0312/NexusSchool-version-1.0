# AI Platform Engine

## Purpose

Provides the AI Platform extension framework for Nexus School ERP, enabling multi-provider AI integration with conversation management, prompt templating, knowledge management, and usage monitoring.

---

## Responsibilities

- Multi-provider AI abstraction (OpenAI, Anthropic, Gemini, Azure OpenAI, Ollama)
- Conversation lifecycle management with context window tracking
- Prompt template versioning and rendering
- Knowledge source indexing and management
- Usage monitoring, token accounting, and quota enforcement
- Audit logging for all AI operations

---

## Architecture

### Provider Pattern
AI providers are interchangeable via a driver-based configuration system. Each provider defines its supported models, rate limits, and token quotas. The `config/ai.php` file centralizes all provider configurations.

### Domain-Driven Design
The AI domain lives under `app/Domains/Ai/` following the established project patterns:
- **DTOs/**: Immutable data transfer objects
- **Enums/**: Backed string enums for status, roles, categories
- **Events/**: Domain events dispatched on state changes
- **Listeners/**: Event handlers for audit, cache, analytics, notifications
- **Policies/**: Authorization checks (admin or ai.* permission)
- **Rules/**: Validation rule sets per entity/mode
- **Services/**: Business logic orchestration

### Models
All AI models are in `app/Models/` with ULID primary keys, soft deletes, and tenant scoping:
- `AiProvider` - Provider configuration
- `AiConversation` - Conversation lifecycle
- `AiMessage` - Individual messages
- `AiKnowledgeSource` - Knowledge base sources
- `AiPromptTemplate` - Reusable prompt templates
- `AiUsageLog` - Immutable usage tracking (no soft deletes)

---

## Entities

### AI Provider
Represents an external or local AI service configuration.
- Supports 5 providers: OpenAI, Anthropic, Gemini, Azure OpenAI, Ollama
- Configurable rate limits, token quotas, supported models
- Provider selection is tenant-scoped and interchangeable

### AI Conversation
Represents a conversation session with an AI provider.
- Context window management (input/output token tracking)
- Status transitions: active → archived/summarized/closed
- System prompt configuration per conversation
- Conversation summaries for long-running sessions

### AI Message
Individual messages within a conversation.
- Role-based: system, user, assistant, function
- Token accounting per message
- Archiving support for context management

### AI Knowledge Source
Represents indexed knowledge for semantic search integration.
- Types: markdown, pdf_metadata, database, text
- Status lifecycle: pending → indexing → indexed (or failed)
- Version tracking with chunk/token counts
- Interfaces only — no vector DB implementation

### AI Prompt Template
Reusable prompt templates with variable substitution.
- 8 categories covering all school roles (student, teacher, parent, finance, HR, analytics, tech support, administrator)
- Versioned with automatic version increment on content changes
- Model-specific parameters (temperature, max_tokens, top_p)
- Variable rendering via `{{variable}}` syntax

### AI Usage Log
Immutable audit trail for all AI operations.
- Token accounting (input, output, total)
- Cost estimation per request
- Latency tracking and cache hit detection
- Quota enforcement with event dispatch on exceedance

---

## Events

| Event | Dispatched When |
|-------|----------------|
| `ConversationStarted` | New conversation created |
| `MessageGenerated` | AI response message added |
| `ProviderChanged` | Provider driver configuration changed |
| `PromptTemplateUpdated` | Template content or category changed |
| `KnowledgeIndexed` | Knowledge source indexing completed |
| `UsageQuotaExceeded` | Token quota limit reached |

---

## Listeners

| Listener | Handles |
|----------|---------|
| `AuditLogger` | All AI events — logs to Laravel log |
| `UsageTracker` | MessageGenerated — records usage entries |
| `CacheClearer` | ProviderChanged, PromptTemplateUpdated, KnowledgeIndexed — clears AI cache |
| `AnalyticsCollector` | ConversationStarted, MessageGenerated, KnowledgeIndexed — analytics data |
| `NotificationDispatcher` | UsageQuotaExceeded — alerts administrators |

---

## Cross-Module Integration

### Reused Infrastructure
- **Queue Engine**: Background AI jobs (ProcessAiMessage, IndexKnowledgeSource, GenerateConversationSummary)
- **Cache Engine**: Prompt caching, provider config caching
- **Storage Engine**: Knowledge source file storage
- **Export Engine**: Usage report exports
- **Analytics Engine**: AI usage analytics collection
- **Notification Engine**: Quota exceeded alerts

### Integration Points
- **Academic**: Student/teacher AI assistants, grade analysis
- **Finance**: Financial report AI analysis
- **Library**: Knowledge source management
- **Communication**: AI-assisted messaging
- **Administration**: Administrative task automation

---

## Security

- Tenant isolation on all AI resources
- Permission-based access: `ai.view`, `ai.create`, `ai.update`, `ai.delete`
- API keys stored in environment variables, never in config
- Sensitive data masking before provider submission
- Usage quotas with automatic enforcement
- Full audit logging of all AI operations

---

## Queue Jobs

| Job | Purpose | Tries | Timeout |
|-----|---------|-------|---------|
| `ProcessAiMessage` | Process AI message in background | 3 | 120s |
| `IndexKnowledgeSource` | Index knowledge source content | 3 | 300s |
| `GenerateConversationSummary` | Generate conversation summary | 3 | 120s |

---

## Console Commands

| Command | Purpose |
|---------|---------|
| `ai:usage-report` | Generate usage report for a school |
| `ai:prune-conversations` | Prune expired conversations (default: 90 days) |

---

## Filament Resources

| Resource | Navigation | Access |
|----------|-----------|--------|
| `AiProviderResource` | AI Platform | CRUD |
| `AiConversationResource` | AI Platform | CRUD |
| `AiMessageResource` | AI Platform (hidden) | Read-only |
| `AiKnowledgeSourceResource` | AI Platform | CRUD |
| `AiPromptTemplateResource` | AI Platform | CRUD |
| `AiUsageLogResource` | AI Platform | Read-only |

---

## Configuration

All configuration is in `config/ai.php`:
- Provider definitions and API key environment variables
- Conversation context window and summarization settings
- Knowledge engine chunking parameters
- Usage quotas (daily/monthly token limits)
- Prompt caching settings
- Security settings

---

## Extension Points

### Implementing a New Provider
1. Add provider config to `config/ai.php`
2. Add driver case to `AiProviderDriver` enum
3. Create provider-specific service class
4. Implement actual LLM API calls in `ProcessAiMessage` job

### Implementing Vector Search
1. Implement `KnowledgeService::search()` method
2. Add embedding generation in `IndexKnowledgeSource` job
3. Integrate with vector database adapter
4. Update `AiKnowledgeSource` configuration schema

---

## Testing

- **Unit Tests**: `tests/Unit/Domains/Ai/` — 5 test files covering all services
- **Feature Tests**: `tests/Feature/Filament/AiResourceTest.php` — Filament resource rendering

---

## Technical Debt

- Actual LLM API calls are not implemented (extension point only)
- Vector database integration is interface-only
- Embedding generation is not implemented
- Speech-to-text, text-to-speech, image generation not implemented
- Autonomous agents not implemented
