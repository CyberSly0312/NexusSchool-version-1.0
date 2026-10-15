# Phase 2 — Domain-Based Tenant Routing

Date: 2026-10-14
Scope: make the request **host authoritative** for school (tenant) panels, resolved
from the canonical `domains` table, fail-closed, with no regressions to existing
panel identifiers/paths. Super Admin remains central-only.

## Objective

- Register tenant hostnames in the stancl `domains` table.
- Resolve the tenant for a school panel request from the matching host, not from
  the authenticated user.
- Fail closed for central hosts, unknown hosts, inactive/deleted schools, and
  cross-tenant access.
- Keep the existing 8 panel IDs/paths unchanged.

## What changed

### Configuration

- `config/tenancy.php` — `central_domains` is now env-driven via `CENTRAL_DOMAINS`
  (comma-separated, trimmed/filtered). Defaults preserve production hosts plus
  the local `.test` hosts and `localhost`/`127.0.0.1`:
  `nexusschool.com,www.nexusschool.com,nexusschool.test,www.nexusschool.test,127.0.0.1,localhost`.
- `.env.example` — added a `CENTRAL_DOMAINS` block; restored `APP_URL=http://localhost`.

### Tenant resolution middleware

- `app/Http/Middleware/InitializeTenancyForPanel.php` — rewritten domain-first.
  Behaviour for every school panel request:
  1. host in `tenancy.central_domains` → `404`;
  2. resolve `School` from `domains` where `domain = host` (soft-deleted schools
     are excluded by the model's global scope) → no match → `404`;
  3. resolved school not `Active` → `403`;
  4. initialize tenancy exactly once; if already initialised for a different
     school → `403`;
  5. authenticated user whose `school_id` differs from the resolved tenant → `403`;
  6. guests are allowed through so they can reach the login screen.
  There is deliberately **no fallback** to the authenticated user's school.

- `app/Domains/School/Models/School.php` — added a typed `domains(): HasMany`
  relation (`HasMany<Domain, $this>`) so static analysis and the resolver share a
  single, explicit hostname relation.

### Panel middleware order

All 7 school panels (`school-admin`, `director`, `teacher`, `student`, `parent`,
`accountant`, `secretary`) now list `InitializeTenancyForPanel` **after**
`ShareErrorsFromSession` (previously it was first, before `StartSession`, where
neither the session nor the authenticated user was available). The Super Admin
panel is untouched (no tenancy middleware).

Declared `->middleware()` list per school panel:

```
EncryptCookies → AddQueuedCookiesToResponse → StartSession →
AuthenticateSession → ShareErrorsFromSession → InitializeTenancyForPanel →
VerifyCsrfToken → SubstituteBindings → DisableBladeIconComponents →
DispatchServingFilamentEvent
```
plus `->authMiddleware([Authenticate])`.

**Actual resolved execution order** (Laravel sorts by middleware priority, see
`route:list -v`):

```
SetUpPanel:{panel} → EncryptCookies → AddQueuedCookiesToResponse → StartSession →
ShareErrorsFromSession → Authenticate (Filament) → AuthenticateSession →
InitializeTenancyForPanel → VerifyCsrfToken → SubstituteBindings →
DisableBladeIconComponents → DispatchServingFilamentEvent
```

Consequence: `Authenticate` → `User::canAccessPanel()` runs **before** tenancy is
initialised. Users of `pending`/`inactive`/`suspended`/deleted schools, or users
with no school, are denied `403` by the authorization gate before domain
resolution. This is fail-closed and intentional; domain resolution still runs for
every remaining (authorised) request and remains authoritative.

### Tests

- `tests/Pest.php`
  - `makeTenant(name, status, domain)` now also inserts a `domains` row
    (`Str::lower($id).'.nexusschool.test'` by default — ULIDs are uppercase while
    `getHost()` is lowercase). The first tenant created in a test also forces the
    app root URL to its host.
  - added `useTenantHost($schoolId)` (forces the request root at that school's
    registered host) and `tenantHost($schoolId)` (looks the host up in `domains`).
- `tests/Feature/Routing/SecurityBaselineTest.php` — rewritten for domain-first
  routing: central vs school hosts, unknown host `404`, guest login on valid host,
  cross-tenant host `403`, deleted-school denial and deleted-domain `404`,
  suspended/pending `403`, ghost-school user `403`, role/panel matrix, scoped-query
  isolation.
- `tests/Feature/Routing/DomainRoutingTest.php` — new: per-host tenant resolution,
  host (not creation order) selects the tenant, multiple registered domains per
  school, domain uniqueness, central-domain defaults, unchanged panel login/logout
  routes, logout on a tenant host.
- `tests/Feature/Filament/AcademicYearResourceTest.php`,
  `tests/Feature/Filament/UserResourceTest.php` — switched manual tenant inserts to
  `makeTenant()` (manual inserts skipped the domain registry, breaking host
  resolution).

## Verification

- `php artisan test tests/Feature/Routing` → **35 passed** (125 assertions).
- `php artisan test tests/Feature/Filament` → **97 passed** (216 assertions).
- `php artisan test` (full suite) → **2134 passed, 0 failed** (5836 assertions, ~625s).
- `vendor\bin\phpstan analyse --no-progress` → `[OK] No errors`.
- `vendor\bin\pint` on changed `app/` files → passed.

## Local development

`.test` hosts (Windows/XAMPP) — add to the hosts file manually (not automated):

```
127.0.0.1 nexusschool.test www.nexusschool.test zinda.nexusschool.test alpha.nexusschool.test
```

- Central (platform) app: `nexusschool.test` → `/super-admin`.
- School panels: `{school-domain}` → `/school-admin`, `/director`, `/teacher`,
  `/student`, `/parent`, `/accountant`, `/secretary`.
- `localhost`/`127.0.0.1` are central: school panels return `404` there by design.
- Served either through Apache VirtualHosts (document root
  `C:\xampp\htdocs\NexusSchool\public`) or `php artisan serve` with a matching host.
- Register a school host by inserting a row in `domains` (`domain`, `tenant_id`).

## Production URLs

- Central: `https://nexusschool.com/super-admin`
- School: `https://zinda.nexusschool.com/{panel}`

## Remaining gaps / notes

- Session cookies are currently host-scoped (host-only). Cross-host SSO is **not**
  implemented and was deliberately not introduced; a shared parent-domain cookie
  would need `SESSION_DOMAIN` plus dedicated cross-host session tests before use.
- Panel IDs and paths are unchanged; no redirects were added at the central host.
- Unrelated pre-existing `/director` dashboard `500` (missing `AcademicResultsChart`
  widget) was intentionally not addressed in this phase.

## Git safety

No commits, resets, cleans, stashes, checkouts, or staging were performed. All
pre-existing modified and untracked files were preserved.
