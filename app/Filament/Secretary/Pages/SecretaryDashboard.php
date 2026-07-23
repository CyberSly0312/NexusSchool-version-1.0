<?php

declare(strict_types=1);

namespace App\Filament\Secretary\Pages;

use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;

class SecretaryDashboard extends Dashboard
{
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::PencilSquare;
    protected static ?string $navigationLabel = 'Mon tableau de bord';
    protected static ?string $title = 'Espace Secrétariat';
    protected static ?int $navigationSort = 0;

    public function getWidgets(): array
    {
        return [];
    }
}
