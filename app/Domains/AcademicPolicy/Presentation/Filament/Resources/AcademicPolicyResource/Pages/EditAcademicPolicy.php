<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Presentation\Filament\Resources\AcademicPolicyResource\Pages;

use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyScope;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyValueType;
use App\Domains\AcademicPolicy\Domain\ValueObjects\PolicyValue;
use App\Domains\AcademicPolicy\Presentation\Filament\Resources\AcademicPolicyResource;
use Filament\Resources\Pages\EditRecord;

class EditAcademicPolicy extends EditRecord
{
    protected static string $resource = AcademicPolicyResource::class;

    protected static ?string $title = 'Modifier la politique académique';

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $type = AcademicPolicyValueType::from((string) $data['value_type']);
        $data['value'] = PolicyValue::coerce($type, $data['value'])->asRaw();

        if ($data['scope'] === AcademicPolicyScope::School->value) {
            $data['academic_year_id'] = null;
            $data['academic_subject_id'] = null;
        }

        return $data;
    }
}
