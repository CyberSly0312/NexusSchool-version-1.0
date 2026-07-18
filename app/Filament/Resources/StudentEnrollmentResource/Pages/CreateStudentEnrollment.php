<?php

declare(strict_types=1);

namespace App\Filament\Resources\StudentEnrollmentResource\Pages;

use App\Filament\Resources\StudentEnrollmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentEnrollment extends CreateRecord
{
    protected static string $resource = StudentEnrollmentResource::class;
}
