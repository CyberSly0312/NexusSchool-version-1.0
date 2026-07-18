<?php

declare(strict_types=1);

namespace App\Domains\Ai\Enums;

enum AiProviderDriver: string
{
    case OpenAI = 'openai';
    case Anthropic = 'anthropic';
    case Gemini = 'gemini';
    case AzureOpenAI = 'azure_openai';
    case Ollama = 'ollama';

    public function label(): string
    {
        return match ($this) {
            self::OpenAI => 'OpenAI',
            self::Anthropic => 'Anthropic',
            self::Gemini => 'Google Gemini',
            self::AzureOpenAI => 'Azure OpenAI',
            self::Ollama => 'Ollama (Local)',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OpenAI => 'success',
            self::Anthropic => 'warning',
            self::Gemini => 'info',
            self::AzureOpenAI => 'primary',
            self::Ollama => 'secondary',
        };
    }

    public static function labels(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases()),
        );
    }
}
