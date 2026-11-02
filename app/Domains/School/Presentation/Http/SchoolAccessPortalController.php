<?php

declare(strict_types=1);

namespace App\Domains\School\Presentation\Http;

use App\Domains\School\Models\School;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * School Access Portal — tenant-aware entry page.
 *
 * Presentation-only controller for the tenant landing page rendered at `/` on
 * every registered school domain. It resolves the current tenant from the
 * request host (through the tenant middleware), fails closed when no tenant can
 * be resolved or the tenant is not active, and renders the school-branded portal
 * with the seven profile entry cards.
 *
 * It never authenticates, never authorizes and never exposes school records:
 * the profile cards are navigation links to the existing Filament panel login
 * routes, where `User::canAccessPanel()` stays the single authorization gate.
 */
final class SchoolAccessPortalController
{
    public function __invoke(Request $request): View
    {
        $tenant = tenant();

        if (! $tenant instanceof School) {
            abort(404);
        }

        if (! $tenant->isActive()) {
            abort(404);
        }

        // An authenticated user may only open the portal of the school they
        // belong to; guests simply reach the public entry page.
        $user = $request->user();

        if ($user instanceof User && (string) $user->school_id !== (string) $tenant->getKey()) {
            abort(403);
        }

        return view('school-access.portal', [
            'school' => $tenant,
            'profiles' => $this->profiles(),
        ]);
    }

    /**
     * The seven school profiles and their existing Filament login routes.
     *
     * @return array<int, array{id: string, label: string, description: string, color: string, icon: string, login_url: string}>
     */
    private function profiles(): array
    {
        $profiles = [
            [
                'id' => 'school-admin',
                'label' => 'School Admin',
                'description' => 'Manage the school administration, users and academic setup.',
                'color' => '#00a2ff',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V8l7-5 7 5v13"/><path d="M9 21v-7h6v7"/></svg>',
                'login_url' => route('filament.school-admin.auth.login'),
            ],
            [
                'id' => 'director',
                'label' => 'Director',
                'description' => 'Oversee the academic leadership and institutional decisions.',
                'color' => '#7c3aed',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>',
                'login_url' => route('filament.director.auth.login'),
            ],
            [
                'id' => 'secretary',
                'label' => 'Secretary',
                'description' => 'Handle records, communications and administrative follow-up.',
                'color' => '#db2777',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>',
                'login_url' => route('filament.secretary.auth.login'),
            ],
            [
                'id' => 'accountant',
                'label' => 'Accountant',
                'description' => 'Manage fees, payments and financial records.',
                'color' => '#0891b2',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="7" width="16" height="14" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M12 13h.01"/><path d="M12 17h.01"/></svg>',
                'login_url' => route('filament.accountant.auth.login'),
            ],
            [
                'id' => 'teacher',
                'label' => 'Teacher',
                'description' => 'Access classroom and academic management tools.',
                'color' => '#059669',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>',
                'login_url' => route('filament.teacher.auth.login'),
            ],
            [
                'id' => 'student',
                'label' => 'Student',
                'description' => 'Access learning, grades and progress tracking.',
                'color' => '#ea580c',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10L12 5 2 10l10 5 10-5z"/><path d="M6 12v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5"/></svg>',
                'login_url' => route('filament.student.auth.login'),
            ],
            [
                'id' => 'parent',
                'label' => 'Parent',
                'description' => 'Follow progress, attendance and academic outcomes.',
                'color' => '#2563eb',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
                'login_url' => route('filament.parent.auth.login'),
            ],
        ];

        return $profiles;
    }
}
