<?php

declare(strict_types=1);

namespace App\Filament\Teacher\Pages;

use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;

class TeacherDashboard extends Dashboard
{
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::AcademicCap;
    protected static ?string $navigationLabel = 'Mon tableau de bord';
    protected static ?string $title = 'Espace Enseignant';
    protected static ?int $navigationSort = 0;

    public function getWidgets(): array
    {
        return [];
    }
}
