<?php

declare(strict_types=1);

namespace App\Filament\Parent\Pages;

use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;

class ParentDashboard extends Dashboard
{
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Users;
    protected static ?string $navigationLabel = 'Mon tableau de bord';
    protected static ?string $title = 'Espace Parent';
    protected static ?int $navigationSort = 0;

    public function getWidgets(): array
    {
        return [];
    }
}
