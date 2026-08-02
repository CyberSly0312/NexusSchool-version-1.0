<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * A self-contained reference to a student's guardian. Guardians may later be
 * upgraded to first-class entities; the value object keeps the domain stable
 * in the meantime.
 */
final readonly class GuardianReference
{
    public const RELATIONSHIPS = [
        'parent',
        'guardian',
        'sibling',
        'other',
    ];

    public string $name;

    public ?string $phone;

    public ?string $email;

    public string $relationship;

    public bool $isPrimary;

    public function __construct(
        string $name,
        ?string $phone = null,
        ?string $email = null,
        string $relationship = 'parent',
        bool $isPrimary = false,
    ) {
        $name = trim($name);
        $relationship = strtolower(trim($relationship));

        if ($name === '') {
            throw new InvalidArgumentException('Guardian name must not be empty.');
        }

        if (! in_array($relationship, self::RELATIONSHIPS, true)) {
            throw new InvalidArgumentException(sprintf(
                'Guardian relationship must be one of: %s.',
                implode(', ', self::RELATIONSHIPS),
            ));
        }

        if ($phone !== null && trim($phone) !== '') {
            new PhoneNumber($phone);
        }

        if ($email !== null && trim($email) !== '') {
            new EmailAddress($email);
        }

        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
        $this->relationship = $relationship;
        $this->isPrimary = $isPrimary;
    }

    public function display(): string
    {
        return trim($this->name.' '.($this->phone !== null ? "({$this->phone})" : ''));
    }

    /**
     * Builds a reference from a raw form array, or null when the row is empty.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromNullableGuardian(array $data): ?self
    {
        $name = trim((string) ($data['name'] ?? ''));

        if ($name === '') {
            return null;
        }

        return new self(
            name: $name,
            phone: isset($data['phone']) && trim((string) $data['phone']) !== '' ? (string) $data['phone'] : null,
            email: isset($data['email']) && trim((string) $data['email']) !== '' ? (string) $data['email'] : null,
            relationship: (string) ($data['relationship'] ?? 'parent'),
            isPrimary: (bool) ($data['is_primary'] ?? false),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'relationship' => $this->relationship,
            'is_primary' => $this->isPrimary,
        ];
    }
}
