<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | The default AI provider driver used for completions when no specific
    | provider is configured for the tenant.
    |
    */

    'default' => env('AI_DEFAULT_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | AI Providers
    |--------------------------------------------------------------------------
    |
    | Configuration for each supported AI provider. The 'driver' determines
    | which service class handles API communication. API keys should be
    | stored in environment variables, never in this config file.
    |
    */

    'providers' => [

        'openai' => [
            'driver' => 'openai',
            'name' => 'OpenAI',
            'api_key_env' => 'OPENAI_API_KEY',
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'default_model' => env('OPENAI_DEFAULT_MODEL', 'gpt-4o'),
            'supported_models' => [
                'gpt-4o',
                'gpt-4o-mini',
                'gpt-4-turbo',
                'gpt-3.5-turbo',
            ],
            'max_tokens' => 4096,
            'timeout' => 120,
        ],

        'anthropic' => [
            'driver' => 'anthropic',
            'name' => 'Anthropic',
            'api_key_env' => 'ANTHROPIC_API_KEY',
            'base_url' => env('ANTHROPIC_BASE_URL', 'https://api.anthropic.com'),
            'default_model' => env('ANTHROPIC_DEFAULT_MODEL', 'claude-sonnet-4-20250514'),
            'supported_models' => [
                'claude-sonnet-4-20250514',
                'claude-3-5-haiku-20241022',
                'claude-3-opus-20240229',
            ],
            'max_tokens' => 4096,
            'timeout' => 120,
        ],

        'gemini' => [
            'driver' => 'gemini',
            'name' => 'Google Gemini',
            'api_key_env' => 'GEMINI_API_KEY',
            'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
            'default_model' => env('GEMINI_DEFAULT_MODEL', 'gemini-1.5-pro'),
            'supported_models' => [
                'gemini-1.5-pro',
                'gemini-1.5-flash',
                'gemini-1.0-pro',
            ],
            'max_tokens' => 4096,
            'timeout' => 120,
        ],

        'azure_openai' => [
            'driver' => 'azure_openai',
            'name' => 'Azure OpenAI',
            'api_key_env' => 'AZURE_OPENAI_API_KEY',
            'base_url' => env('AZURE_OPENAI_BASE_URL'),
            'deployment_name' => env('AZURE_OPENAI_DEPLOYMENT'),
            'api_version' => env('AZURE_OPENAI_API_VERSION', '2024-02-01'),
            'default_model' => env('AZURE_OPENAI_DEFAULT_MODEL', 'gpt-4o'),
            'supported_models' => [
                'gpt-4o',
                'gpt-4-turbo',
                'gpt-35-turbo',
            ],
            'max_tokens' => 4096,
            'timeout' => 120,
        ],

        'ollama' => [
            'driver' => 'ollama',
            'name' => 'Ollama (Local)',
            'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
            'default_model' => env('OLLAMA_DEFAULT_MODEL', 'llama3'),
            'supported_models' => [
                'llama3',
                'mistral',
                'codellama',
                'phi3',
            ],
            'max_tokens' => 4096,
            'timeout' => 300,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Conversation Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for AI conversations including context window limits
    | and summarization thresholds.
    |
    */

    'conversation' => [
        'default_context_window' => (int) env('AI_DEFAULT_CONTEXT_WINDOW', 128000),
        'summarize_threshold' => (int) env('AI_SUMMARIZE_THRESHOLD', 50),
        'max_messages_per_conversation' => (int) env('AI_MAX_MESSAGES', 500),
        'auto_title_generation' => (bool) env('AI_AUTO_TITLE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Knowledge Engine
    |--------------------------------------------------------------------------
    |
    | Settings for the knowledge indexing and retrieval engine.
    |
    */

    'knowledge' => [
        'max_file_size_mb' => (int) env('AI_KNOWLEDGE_MAX_FILE_MB', 50),
        'supported_types' => ['markdown', 'pdf_metadata', 'database', 'text'],
        'chunk_size' => (int) env('AI_KNOWLEDGE_CHUNK_SIZE', 1000),
        'chunk_overlap' => (int) env('AI_KNOWLEDGE_CHUNK_OVERLAP', 200),
    ],

    /*
    |--------------------------------------------------------------------------
    | Usage Quotas
    |--------------------------------------------------------------------------
    |
    | Default usage quotas for AI operations per tenant.
    |
    */

    'quotas' => [
        'daily_tokens' => (int) env('AI_DAILY_TOKEN_LIMIT', 1000000),
        'monthly_tokens' => (int) env('AI_MONTHLY_TOKEN_LIMIT', 30000000),
        'max_concurrent_requests' => (int) env('AI_MAX_CONCURRENT', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Prompt Caching
    |--------------------------------------------------------------------------
    |
    | Configuration for prompt caching to reduce redundant API calls.
    |
    */

    'cache' => [
        'enabled' => (bool) env('AI_CACHE_ENABLED', true),
        'ttl_minutes' => (int) env('AI_CACHE_TTL', 60),
        'prefix' => 'ai:',
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    |
    | Security settings for AI operations.
    |
    */

    'security' => [
        'require_authorization' => (bool) env('AI_REQUIRE_AUTHORIZATION', true),
        'audit_all_requests' => (bool) env('AI_AUDIT_REQUESTS', true),
        'mask_sensitive_data' => (bool) env('AI_MASK_SENSITIVE', true),
        'blocked_patterns' => [],
    ],

];
