<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use Filament\Pages\Dashboard;

class SuperAdminDashboard extends Dashboard
{
    protected static \BackedEnum|string|null $navigationIcon = null;
    protected static ?string $title = 'Administration centrale';
    protected static ?int $navigationSort = 0;

    public function getWidgets(): array
    {
        return [];
    }
}
