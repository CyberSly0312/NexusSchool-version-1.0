<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Panel;

/**
 * Centralized, server-side authority for panel access.
 *
 * Authorization is derived exclusively from the authenticated user's database
 * roles and account/school state. UI selections never grant access.
 */
final class PanelAccessRegistry
{
    /**
     * Logical groups are presentation only; they do not grant permissions.
     *
     * @return array<string, array{label: string, panels: list<string>}>
     */
    public function groups(): array
    {
        return [
            'management' => [
                'label' => 'Management',
                'panels' => ['school-admin', 'director', 'secretary', 'accountant'],
            ],
            'staff' => [
                'label' => 'Staff',
                'panels' => ['teacher'],
            ],
            'family' => [
                'label' => 'Family',
                'panels' => ['student', 'parent'],
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public function panelIds(): array
    {
        return [
            'super-admin',
            'admin',
            'school-admin',
            'director',
            'secretary',
            'accountant',
            'teacher',
            'student',
            'parent',
        ];
    }

    /**
     * @return list<string>
     */
    public function rolesForPanel(string $panelId): array
    {
        return match ($panelId) {
            'admin', 'school-admin' => ['school_admin'],
            'director' => ['director'],
            'teacher' => ['teacher'],
            'student' => ['student'],
            'parent' => ['parent'],
            'accountant' => ['accountant'],
            'secretary' => ['secretary'],
            'super-admin' => ['super_admin'],
            default => [],
        };
    }

    public function canUserAccessPanel(User $user, Panel|string $panel): bool
    {
        $panelId = $panel instanceof Panel ? $panel->getId() : $panel;

        if (! $user->isActive()) {
            return false;
        }

        if ($user->hasRole('super_admin')) {
            return $panelId === 'super-admin';
        }

        if ($panelId === 'super-admin') {
            return false;
        }

        $panelRoles = $this->rolesForPanel($panelId);

        if ($panelRoles === [] || $panelRoles === ['super_admin']) {
            return false;
        }

        if (! $user->hasAnyRole($panelRoles)) {
            return false;
        }

        return $user->hasActiveSchool();
    }

    /**
     * @return list<array{id: string, label: string, url: string, group: string|null}>
     */
    public function authorizedPanelsFor(User $user): array
    {
        $panels = [];

        foreach ($this->selectablePanelIds() as $panelId) {
            if (! $this->canUserAccessPanel($user, $panelId)) {
                continue;
            }

            $panel = Filament::getPanels()[$panelId] ?? null;

            if (! $panel instanceof Panel) {
                continue;
            }

            $panels[] = [
                'id' => $panelId,
                'label' => $this->labelForPanel($panelId),
                'url' => $panel->getUrl(),
                'group' => $this->groupForPanel($panelId),
            ];
        }

        return $panels;
    }

    /**
     * Panel IDs that can be presented as post-authentication destinations.
     * The legacy "admin" alias is accepted for authorization checks but is not
     * presented as a separate destination.
     *
     * @return list<string>
     */
    public function selectablePanelIds(): array
    {
        return [
            'super-admin',
            'school-admin',
            'director',
            'secretary',
            'accountant',
            'teacher',
            'student',
            'parent',
        ];
    }

    public function isSelectablePanel(string $panelId): bool
    {
        return in_array($panelId, $this->selectablePanelIds(), true);
    }

    public function labelForPanel(string $panelId): string
    {
        return match ($panelId) {
            'super-admin' => 'Super Admin',
            'school-admin', 'admin' => 'School Admin',
            'director' => 'Director',
            'secretary' => 'Secretary',
            'accountant' => 'Accountant',
            'teacher' => 'Teacher',
            'student' => 'Student',
            'parent' => 'Parent',
            default => str($panelId)->replace('-', ' ')->title()->toString(),
        };
    }

    public function groupForPanel(string $panelId): ?string
    {
        foreach ($this->groups() as $key => $group) {
            if (in_array($panelId, $group['panels'], true)) {
                return $key;
            }
        }

        return null;
    }
}
