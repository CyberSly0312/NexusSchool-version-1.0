<?php

declare(strict_types=1);

namespace App\Filament\Student\Pages;

use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;

class StudentDashboard extends Dashboard
{
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::User;
    protected static ?string $navigationLabel = 'Mon tableau de bord';
    protected static ?string $title = 'Espace Élève';
    protected static ?int $navigationSort = 0;

    public function getWidgets(): array
    {
        return [];
    }
}
