<?php

declare(strict_types=1);

namespace App\Filament\SchoolAdmin\Widgets;

use App\Domains\School\Models\School;
use App\Models\AcademicClass;
use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class ConfigurationAlertsWidget extends Widget
{
    protected static ?int $sort = 8;

    protected string $view = 'filament.school-admin.widgets.configuration-alerts';

    public array $alerts = [];

    public function mount(): void
    {
        $this->loadAlerts();
    }

    public function loadAlerts(): void
    {
        $school = School::find(tenant('id'));
        $settings = $school?->settings ?? [];
        $this->alerts = [];

        if (AcademicYear::where('school_id', tenant('id'))->count() === 0) {
            $this->alerts[] = [
                'type' => 'danger',
                'title' => 'Aucune année scolaire',
                'message' => 'Créez au moins une année scolaire pour commencer.',
                'action' => 'academic-years',
            ];
        }

        if (AcademicClass::where('school_id', tenant('id'))->count() === 0) {
            $this->alerts[] = [
                'type' => 'warning',
                'title' => 'Aucune classe configurée',
                'message' => 'Ajoutez des classes pour organiser vos élèves.',
                'action' => 'academic-classes',
            ];
        }

        if (Subject::where('school_id', tenant('id'))->count() === 0) {
            $this->alerts[] = [
                'type' => 'warning',
                'title' => 'Aucune matière configurée',
                'message' => 'Définissez les matières enseignées dans votre établissement.',
                'action' => 'subjects',
            ];
        }

        if (User::where('school_id', tenant('id'))
            ->whereHas('roles', fn ($q) => $q->where('name', 'teacher'))
            ->count() === 0) {
            $this->alerts[] = [
                'type' => 'info',
                'title' => 'Aucun enseignant',
                'message' => 'Ajoutez des enseignants pour attribuer des cours.',
                'action' => 'users',
            ];
        }

        if (! isset($settings['fees'])) {
            $this->alerts[] = [
                'type' => 'info',
                'title' => 'Frais scolaires non configurés',
                'message' => 'Paramétrez les cycles de paiement et les frais de retard.',
                'action' => 'fee-configuration',
            ];
        }

        if (! isset($settings['pdf'])) {
            $this->alerts[] = [
                'type' => 'info',
                'title' => 'Documents PDF non configurés',
                'message' => 'Configurez l\'en-tête, le pied de page et le cachet officiel.',
                'action' => 'pdf-settings',
            ];
        }
    }
}
