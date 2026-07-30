<?php

declare(strict_types=1);

namespace App\Filament\SchoolAdmin\Pages;

use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;

class SchoolAdminDashboard extends Dashboard
{
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Cog6Tooth;

    protected static ?string $navigationLabel = 'Tableau de bord';

    protected static ?string $title = 'Administration de l\'établissement';

    protected static ?int $navigationSort = 0;

    public function getWidgets(): array
    {
        return [
            \App\Filament\SchoolAdmin\Widgets\ConfigStatusWidget::class,
            \App\Filament\SchoolAdmin\Widgets\ConfigAlertsWidget::class,
            \App\Filament\SchoolAdmin\Widgets\StudentsCountWidget::class,
            \App\Filament\SchoolAdmin\Widgets\TeachersCountWidget::class,
            \App\Filament\SchoolAdmin\Widgets\ClassesCountWidget::class,
            \App\Filament\SchoolAdmin\Widgets\SubjectsCountWidget::class,
        ];
    }
}
