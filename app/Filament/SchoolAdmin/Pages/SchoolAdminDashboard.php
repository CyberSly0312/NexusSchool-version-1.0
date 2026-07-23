<?php

declare(strict_types=1);

namespace App\Filament\SchoolAdmin\Pages;

use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;

class SchoolAdminDashboard extends Dashboard
{
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Cog6Tooth;
    protected static ?string $navigationLabel = 'Tableau de bord';
    protected static ?string $title = 'Administration de l\'école';
    protected static ?int $navigationSort = 0;

    public function getWidgets(): array
    {
        return [];
    }
}
