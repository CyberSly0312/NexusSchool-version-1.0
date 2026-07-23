<?php

declare(strict_types=1);

namespace App\Filament\Accountant\Pages;

use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;

class AccountantDashboard extends Dashboard
{
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Banknotes;
    protected static ?string $navigationLabel = 'Mon tableau de bord';
    protected static ?string $title = 'Espace Comptable';
    protected static ?int $navigationSort = 0;

    public function getWidgets(): array
    {
        return [];
    }
}
