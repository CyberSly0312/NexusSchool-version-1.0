<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Models;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Per-school registry of the publication channels enabled for the Academic
 * Communication Engine (SPR-012). Only enabled channels are offered to the
 * publication dispatcher. Future channels (Email, SMS, Push) are defined in
 * the DocumentChannel enum and enabled here — no redesign required.
 */
class PublicationChannel extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'publication_channels';

    protected $fillable = [
        'uuid',
        'school_id',
        'key',
        'label',
        'is_enabled',
        'config',
    ];

    protected function casts(): array
    {
        return [
            'key' => DocumentChannel::class,
            'is_enabled' => 'boolean',
            'config' => 'array',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\AcademicCommunication\PublicationChannelFactory();
    }

    public function isEnabled(): bool
    {
        return $this->is_enabled;
    }

    public function displayName(): string
    {
        return $this->label !== null && trim($this->label) !== ''
            ? $this->label
            : $this->key->label();
    }
}
