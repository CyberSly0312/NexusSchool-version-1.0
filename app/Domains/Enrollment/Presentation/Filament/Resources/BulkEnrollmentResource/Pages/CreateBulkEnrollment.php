<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Presentation\Filament\Resources\BulkEnrollmentResource\Pages;

use App\Domains\Enrollment\Application\DTOs\CreateEnrollmentData;
use App\Domains\Enrollment\Application\Services\EnrollmentService;
use App\Domains\Enrollment\Domain\Exceptions\DuplicateEnrollmentException;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Enrollment\Presentation\Filament\Resources\BulkEnrollmentResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class CreateBulkEnrollment extends CreateRecord
{
    protected static string $resource = BulkEnrollmentResource::class;

    protected static ?string $title = 'Inscription groupée';

    protected function handleRecordCreation(array $data): Model
    {
        $schoolId = tenant()?->id ?? auth()->user()?->school_id ?? '';
        $studentIds = (array) ($data['student_ids'] ?? []);
        $enrolledAt = $data['enrolled_at'] ?? now()->toDateString();

        $service = app(EnrollmentService::class);
        $created = 0;
        $skipped = 0;
        $errors = [];
        $createdModel = null;

        foreach ($studentIds as $studentId) {
            $dto = new CreateEnrollmentData(
                schoolId: $schoolId,
                studentId: (int) $studentId,
                academicCalendarYearId: (int) $data['academic_calendar_year_id'],
                academicProgramId: (int) $data['academic_program_id'],
                educationLevelId: (int) $data['education_level_id'],
                schoolClassId: (int) $data['school_class_id'],
                classSectionId: isset($data['class_section_id']) ? (int) $data['class_section_id'] : null,
                enrolledAt: $enrolledAt,
            );

            try {
                $createdModel = $service->enroll($dto, auth()->user());
                $created++;
            } catch (DuplicateEnrollmentException) {
                $skipped++;
            } catch (Throwable $e) {
                $skipped++;
                $errors[] = $e->getMessage();
            }
        }

        $notification = Notification::make()
            ->title("{$created} élève(s) inscrit(s)");

        if ($skipped > 0) {
            $body = "{$skipped} élève(s) ignoré(s) (déjà inscrits ou données invalides).";

            if ($errors !== []) {
                $body .= ' Erreur : '.implode(' | ', array_unique($errors));
            }

            $notification->body($body)->warning();
        } else {
            $notification->success();
        }

        $notification->send();

        return $createdModel ?? Enrollment::query()
            ->forSchool($schoolId)
            ->latest('id')
            ->firstOrNew(['school_id' => $schoolId]);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('create');
    }
}
