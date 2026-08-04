<?php

declare(strict_types=1);

namespace App\Filament\SchoolAdmin\Widgets;

use App\Domains\School\Models\School;
use App\Models\AcademicClass;
use App\Models\AcademicYear;
use App\Models\User;
use Filament\Widgets\Widget;

class ConfigAlertsWidget extends Widget
{
    protected string $view = 'filament.school-admin.widgets.config-alerts';

    protected static ?int $sort = 1;

    public array $alerts = [];

    public function mount(): void
    {
        $this->alerts = $this->getAlerts();
    }

    protected function getAlerts(): array
    {
        $school = School::find(tenant('id'));
        $settings = $school?->settings ?? [];
        $tenantId = tenant('id');
        $alerts = [];

        if (empty($school?->logo_url)) {
            $alerts[] = ['type' => 'warning', 'message' => 'Aucun logo configuré'];
        }

        if (empty($settings['pdf']['pdf_stamp_path'])) {
            $alerts[] = ['type' => 'warning', 'message' => 'Aucun cachet configuré'];
        }

        if (empty($settings['pdf']['pdf_signature_path'])) {
            $alerts[] = ['type' => 'warning', 'message' => 'Signature du Directeur manquante'];
        }

        if (! AcademicYear::where('school_id', $tenantId)->where('status', 'active')->exists()) {
            $alerts[] = ['type' => 'danger', 'message' => 'Aucune année scolaire active'];
        }

        if (AcademicClass::where('school_id', $tenantId)->count() === 0) {
            $alerts[] = ['type' => 'warning', 'message' => 'Aucune classe créée'];
        }

        if (User::where('school_id', $tenantId)->whereHas('roles', fn ($q) => $q->where('name', 'teacher'))->count() === 0) {
            $alerts[] = ['type' => 'warning', 'message' => 'Aucun enseignant créé'];
        }

        return $alerts;
    }
}
