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
 * with a single centralized gateway sign-in entry point.
 *
 * It never authenticates, never authorizes and never exposes school records.
 * Panel access is resolved server-side after authentication via
 * `PanelAccessRegistry` and enforced by `User::canAccessPanel()`.
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
        ]);
    }
}
