<?php

declare(strict_types=1);

namespace App\Domains\Library\Enums;

use App\Models\Student;
use App\Models\User;

enum BorrowerType: string
{
    case Student = 'student';
    case Teacher = 'teacher';

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Élève',
            self::Teacher => 'Enseignant',
        };
    }

    public function modelClass(): string
    {
        return match ($this) {
            self::Student => Student::class,
            self::Teacher => User::class,
        };
    }
}
