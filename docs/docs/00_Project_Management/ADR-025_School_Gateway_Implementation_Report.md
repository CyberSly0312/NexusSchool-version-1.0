# ADR-025 — School Gateway Implementation Milestone Report

| Field | Value |
|---|---|
| Status | **Gateway: first implementation milestone completed with focused automated tests — OAuth tenant-binding: remediation implemented and focused tests passed — Existing API infrastructure resource isolation: focused tests passed — NOT BROWSER-VALIDATED — NOT PRODUCTION-APPROVED** |
| Date | 2026-11-23 (updated 2026-11-24 for OAuth/API security verification documentation) |
| Related ADR | `docs/ADR/ADR-025.md` — **PROPOSED** (not accepted; no implementation/production authorization granted by this report) |
| Branch / HEAD | `main` / `fab58b6dd93ebc7d0ebaeabfb8d1f81ac0a800d6` |
| Scope | Documentation of completed focused gateway milestone, OAuth tenant-binding remediation, and API infrastructure tenant-isolation verification |
| Application code modified by this documentation task | **None** |

---

## 1. Objective

Record the implementation evidence, validation evidence, explicit boundaries, and unresolved risks for the first controlled milestone of ADR-025: centralized tenant-host-scoped school authentication gateway, server-side panel authorization, post-auth panel routing, tenant isolation, and focused security controls.

This report does **not** accept ADR-025, authorize production use, authorize additional phases, or close governance interlocks.

## 2. Governance / ADR-025 Status

- **ADR-025 status remains `PROPOSED`**. It must not be marked ACCEPTED based on implementation or focused tests passing.
- No sprint/implementation/production authorization is granted by this report.
- Governance interlocks referenced by ADR-025 remain unchanged by this report (including ADR-024 Proposed / SPR-017 not authorized; SPR-014 closure/waiver interlock; ADR-023 reserved).
- Implementation evidence and outstanding decisions are recorded here separately from ADR status.

## 3. Implemented and Tested (Focused Automated Evidence)

| Area | Evidence | Status |
|---|---|---|
| Centralized school gateway routes on tenant host | `routes/tenant.php` adds `/login`, `/panel-select`, `/no-panel-access`, `/logout` under existing `web` + `InitializeTenancyForPanel` | Implemented; focused automated tests pass |
| No pre-auth role selection | Portal shows single gateway CTA; gateway login accepts credentials only; no role/panel/permission trusted from login form | Implemented; focused automated tests pass |
| Successful login / invalid credentials | `SchoolGatewayAuthenticationTest` | PASS |
| Single authorized panel redirection | `SchoolGatewayAuthenticationTest` | PASS |
| Multiple authorized panel selection (authorized only) | `SchoolGatewayAuthenticationTest` | PASS |
| Unauthorized panel selection rejection | `SchoolGatewayAuthenticationTest` | PASS |
| Direct unauthorized panel URL rejection | `SchoolGatewayAuthenticationTest`, existing routing/security tests | PASS |
| Unmapped role denial | `SchoolGatewayAuthenticationTest` | PASS |
| Cross-tenant login/context rejection | `SchoolGatewayAuthenticationTest` | PASS |
| Missing tenant fail-closed | `SchoolGatewayAuthenticationTest`, existing routing tests | PASS |
| Session regeneration and logout invalidation | `SchoolGatewayAuthenticationTest` | PASS |
| Login throttling | `SchoolGatewayAuthenticationTest` (IP + account identifier throttling via `RateLimiter`) | PASS |
| Existing panel access regression | `SchoolGatewayAuthenticationTest` (`PanelAccessRegistry` preserves legacy role mapping, including `admin` alias) | PASS |
| Existing tenant-isolation regression | `SchoolGatewayAuthenticationTest`, `DirectorTenantIsolationTest` | PASS |
| Portal/gateway UI regression | `SchoolAccessPortalTest` updated for centralized gateway CTA/no pre-auth role selection | PASS |
| Existing Filament login compatibility / routing regressions | `tests/Feature/Routing` suite | PASS |
| French translation support for gateway views | Gateway/portal views use `__()`; gateway keys added to `lang/fr.json` | Implemented; automated tests pass after locale-agnostic assertion fix |

## 4. Implemented but Only Code-Reviewed / Limited Validation

| Area | Notes | Status |
|---|---|---|
| CSRF protection | Gateway forms include `@csrf`; routes under `web` middleware. No dedicated CSRF-rejection browser/E2E test added in validation pass. | Implemented; code-reviewed/limited |
| Authentication audit event recording | `AuditLog` records success/failure/throttle/denial/rejection/logout; audit writes are best-effort (caught, non-blocking). No audit-row assertions added in validation pass. | Implemented; code-reviewed/limited |
| Inactive/suspended/disabled account handling | `PanelAccessRegistry` requires active user/school; gateway logs out inactive users during post-auth routing. Focused dedicated inactive-account browser flow not executed. | Implemented; limited validation |
| Production cookie hardening | Relies on existing Laravel session/env configuration; no new env enforcement added. | Partially configuration-dependent; not production-validated |
| Trusted hosts/proxies/rate-limit store configuration | Expected to use existing deployment configuration; no deployment validation performed. | Configuration-dependent; not production-validated |
| Back-button/browser history behavior after logout | HTTP session invalidation/regeneration verified; real browser back-button/cache/history behavior not validated. | HTTP-level pass; not browser-validated |

## 5. Not Implemented / Not Fully Completed

- MFA.
- Family/Parent/Student relation-based authorization and family data ownership substrate.
- Refresh-token flow (none exists currently); any future implementation must include tenant-binding revalidation.
- API tenant-host/domain resolution middleware for API routes (current protection relies on token binding and resource-level ownership enforcement).
- Domain-record API endpoints for students/parents/classes/enrolments/grades/attendance/fees/payments (not present in inspected API routes; future endpoints must implement and test equivalent tenant scoping).
- Session, remember-token, API-token, and Spatie permission-cache revocation on role/school/status changes.
- Dedicated audit persistence verification/monitoring workflow beyond best-effort writes.
- Production environment hardening enforcement/verification (cookies, trusted hosts, proxies, distributed rate-limit store).
- New Family portal/interface.

## 6. Not Validated in a Real Browser

- Real browser E2E validation was **not** performed. No Dusk/Playwright/Pest-browser tooling is available in the environment.
- No screenshots captured.
- Mobile/responsive behavior was only code-inspected; no real mobile browser/device validation.
- Browser back-button/history/cache behavior after logout was not validated in a real browser.

## 7. Not Approved for Production

This milestone is **not approved for production**. Reasons include: ADR-025 remains PROPOSED; unresolved security/governance items remain; no real browser/mobile validation; no production env hardening verification; OAuth refresh-token flow is absent and broader API tenancy hardening remains limited; revocation/MFA/family authorization not implemented; audit persistence and production env hardening not verified. No broader sprint or production authorization is implied.

## 8. Gateway Implementation Details

### 8.1 Routes and flow

- **Portal**: `GET /` (tenant host) renders centralized gateway CTA only; no pre-auth role/profile selection.
- **Login**: `GET /login`, `POST /login` (`school.gateway.login`, `school.gateway.login.submit`).
- **Post-auth selection**: `GET /panel-select`, `POST /panel-select`.
- **No access**: `GET /no-panel-access`.
- **Logout**: `POST /logout`.
- All gateway routes are inside `web` + `App\Http\Middleware\InitializeTenancyForPanel`.

Flow:
1. Host-first tenant resolution via `InitializeTenancyForPanel`; fail closed on missing/invalid tenant.
2. User submits credentials only; no role/panel/permission accepted from form.
3. `RateLimiter` checks IP and tenant+normalized account identifier before attempt; failures/throttles audited.
4. On successful auth: session regenerated, login success audited.
5. `PanelAccessRegistry` computes authorized selectable panels from DB roles + active user + active school.
6. 1 panel => redirect; >1 => authorized selector only; 0/unmapped/inactive/no active school => safe denial/no-access and logout/invalidation where appropriate.
7. Panel selection POST is server-revalidated; unauthorized selection returns 403 and is audited.
8. Logout audits, `Auth::logout()`, invalidates session, regenerates CSRF token, redirects to gateway login.

### 8.2 PanelAccessRegistry / authorization

- `App\Services\PanelAccessRegistry` is the server-side mapping authority for selectable panel destinations.
- Derives authorization from authenticated DB roles and account/school state; UI never grants access.
- Preserves existing mapped panel behavior; legacy `admin` alias authorized but not exposed as separate selectable destination.
- `super_admin` constrained to `super-admin` central panel only.
- Unmapped roles denied by default.
- `App\Models\User::canAccessPanel()` delegates to registry.

### 8.3 Tenant isolation / fail closed

- Host-first tenant resolution remains authoritative (`InitializeTenancyForPanel`).
- No authenticated-user fallback for tenant resolution.
- Unknown/unresolved tenant fails closed (404).
- Cross-tenant authenticated access denied.
- Existing panel IDs/paths preserved.

### 8.4 Security controls

- Session regeneration on login.
- Session invalidate + CSRF token regeneration on logout.
- CSRF protection through `web` middleware and form tokens.
- Login throttling by IP and tenant+account identifier.
- Generic invalid-credentials/throttle messages.
- Unauthorized panel selection/direct URL denial.
- Audit events: `auth.login_succeeded`, `auth.login_failed`, `auth.login_throttled`, `auth.panel_access_denied`, `auth.panel_selection_rejected`, `auth.logout`.

### 8.5 French translation support

- Gateway and portal views use translation keys via `__()`.
- Gateway-related keys added to `lang/fr.json`.
- Tests updated to be locale-agnostic where gateway i18n affected assertions.

### 8.6 Existing Filament login compatibility

- Existing Filament panel IDs/paths and panel login routes preserved.
- No new Filament panels created.
- No panel ID/path changes.

## 9. OAuth Tenant-Binding Remediation

### 9.1 Root cause

OAuth issuance and validation previously lacked consistent client/user/token/school binding. In particular, token issuance could resolve a user globally by email without enforcing `user.school_id === client.school_id`, and token validation did not consistently enforce `token.school_id == client.school_id == user.school_id` or initialized-tenant context consistency. This created a tenant-binding/data-isolation risk.

### 9.2 Remediation

Remediation was implemented in:
- `app/Domains/Api/Services/OAuthService.php`
- `app/Domains/Api/Services/ApiTokenService.php`

The remediation is data/token-level binding for the current OAuth flow. No public API contract change was intended.

### 9.3 Issuance checks

Token issuance now enforces:
- **Active client and school**: client must have a valid `school_id`; client school must exist and be active. If tenancy is initialized, current tenant key must match client school.
- **User belongs to client's school**: user lookup is scoped by `email` and `client.school_id`; user school must match client school.
- **Active user and school**: user must be active and have an active school (`hasActiveSchool()`).
- **Generic failure behavior**: invalid credentials/client/tenant-binding failures return generic validation errors (no tenant/user/client existence oracle).
- **Token creation binding**: `ApiTokenService::create()` sets token `school_id` from client and, when `user_id` is provided, requires the user to exist in the same school and be active.

### 9.4 Token validation checks

Token validation now fails closed unless:
- Token is found by hash and is active/unexpired.
- Client and user exist and are expected model types.
- Client is active and client school exists/is active.
- `token.school_id == client.school_id == user.school_id` and IDs are non-empty.
- User is active, has active school, and `user.school_id` matches token school.
- If tenancy is initialized, current tenant key matches token school.

Invalid/mismatched/inactive context returns `null`, resulting in 401 invalid/expired token behavior.

### 9.5 Refresh-token flow

**No refresh-token flow currently exists** in the inspected codebase (no refresh endpoint/model/columns). Cross-tenant refresh validation is not applicable to the current implementation. Any future refresh-token flow must persist tenant binding and revalidate client/user/token/tenant consistency on refresh.

## 10. API Resource Tenant Isolation Verification

### 10.1 Investigation

Inspected:
- `routes/api.php`
- `app/Http/Middleware/ValidateApiToken.php`
- `app/Http/Controllers/Api/V1/OAuthController.php`
- `app/Http/Controllers/Api/V1/ApiClientController.php`
- `app/Http/Controllers/Api/V1/ApiTokenController.php`
- `app/Http/Controllers/Api/V1/WebhookController.php`
- `app/Http/Controllers/Api/V1/AuditLogController.php`
- `app/Domains/Api/Services/OAuthService.php`
- `app/Domains/Api/Services/ApiTokenService.php`
- `app/Domains/Api/Services/ApiClientService.php`
- `app/Domains/Api/Services/WebhookService.php`

Current exposed API v1 routes are infrastructure/OAuth resources only: OAuth token/me/revoke, API clients, client tokens, webhooks/deliveries, and audit logs. **No domain-record API endpoints for students, parents, classes, enrolments, grades, attendance, or fees/payments currently exist in the inspected API routes.**

### 10.2 Query-scoping behavior

- **`ValidateApiToken`**: authenticates bearer token through `OAuthService::validateToken()` and injects `api_token`, `api_client`, `api_user`; missing/invalid token fails closed with 401.
- **`ApiClientController`**: collection scoped by authenticated `api_client.school_id`; resource show/update/delete/disable/enable/regenerate-secret enforce `ensureSchoolAccess()` against `api_client.school_id`; create forces `school_id` from authenticated client.
- **`ApiTokenController`**: client access checked via `ensureSchoolAccess()`; token operations scoped through client ownership checks; token creation bound to client.
- **`WebhookController`/`WebhookService`**: client/resource ownership checked via `ensureSchoolAccess()`; webhook creation binds `api_client_id` and `school_id` from client.
- **`AuditLogController`**: base query always scoped by `client.school_id`; detail lookup uses `where('school_id', $client->school_id)->findOrFail($id)`.

For currently exposed infrastructure API resources, query scoping/ownership checks are anchored to the authenticated token's client school. This is representative evidence for exposed resources, not proof for unexposed domain-record endpoints.

### 10.3 Focused regression test

Added: `tests/Feature/Api/ApiTenantIsolationTest.php`.

The test uses isolated School A/B data and exercises actual API endpoints against an exposed school-owned resource (`ApiClient`) to verify:
- Valid School A token can access School A resource.
- Same token cannot retrieve School B record by ID.
- Same token cannot list School B records.
- Same token cannot modify School B record.
- Same token cannot delete School B record.
- Request payload cannot override token school ownership on create.
- Missing/invalid token context fails closed.
- Existing legitimate API behavior remains functional.

**Exact results:**
- `php artisan test tests/Feature/Api/ApiTenantIsolationTest.php --compact` — **PASS — 8 passed (17 assertions)**
- `php artisan test tests/Feature/Api --compact` — **PASS — 33 passed (57 assertions)**
- `vendor/bin/pint --test tests/Feature/Api/ApiTenantIsolationTest.php` — **PASS**

### 10.4 Limitations

- **No domain-record API endpoints** for students/parents/classes/enrolments/grades/attendance/fees/payments currently exist in inspected API routes. Their future endpoints must implement and test equivalent tenant scoping.
- The isolation tests are **representative, not exhaustive** across every token/webhook/audit-log endpoint permutation.
- API routes do **not** currently use tenant-host/domain resolution middleware; current protection relies on token binding and resource-level ownership enforcement.
- No browser/production validation performed.

## 11. Automated Test Evidence

| Command | Result |
|---|---|
| `php artisan test tests/Feature/Auth/SchoolGatewayAuthenticationTest.php --compact` | **PASS — 14 passed (53 assertions)** |
| `php artisan test tests/Feature/Routing/SchoolAccessPortalTest.php --compact` (after portal update) | **PASS — 12 passed (26 assertions)** |
| `php artisan test tests/Feature/Routing --compact` | **PASS — 89 passed (323 assertions)** |
| `php artisan test tests/Feature/Filament/DirectorTenantIsolationTest.php --compact` | **PASS — 10 passed (71 assertions)** |
| `php artisan test tests/Feature/Routing/SchoolAccessPortalTest.php tests/Feature/Routing/SuperAdminLoginTest.php --compact` (after i18n fix) | **PASS — 16 passed (45 assertions)** |
| `vendor/bin/pint --test` on changed PHP files (gateway implementation pass) | **PASS** |
| `vendor/bin/pint --test` on changed PHP/views/lang-relevant files (validation pass) | **PASS** |
| `vendor/bin/phpstan analyse app/Services/PanelAccessRegistry.php app/Http/Controllers/Auth/SchoolGatewayAuthController.php app/Models/User.php app/Domains/School/Presentation/Http/SchoolAccessPortalController.php --memory-limit=512M --no-progress` | **PASS — no errors** (project phpstan.neon, level 5) |
| `php artisan test tests/Feature/Api/OAuthApiTest.php --compact` | **PASS — 11 passed (22 assertions)** |
| `php artisan test tests/Feature/Api --compact` | **PASS — 33 passed (57 assertions)** |
| `php artisan test tests/Unit/Domains/Api --compact` | **PASS — 64 passed (119 assertions)** |
| `vendor/bin/pint app/Domains/Api/Services/OAuthService.php app/Domains/Api/Services/ApiTokenService.php tests/Feature/Api/OAuthApiTest.php` | **PASS** |
| `vendor/bin/phpstan analyse app/Domains/Api/Services/OAuthService.php app/Domains/Api/Services/ApiTokenService.php --memory-limit=512M --no-progress` | **PASS — no errors** (project phpstan.neon, level 5) |
| `php artisan test tests/Feature/Api/ApiTenantIsolationTest.php --compact` | **PASS — 8 passed (17 assertions)** |
| `vendor/bin/pint --test tests/Feature/Api/ApiTenantIsolationTest.php` | **PASS** |

Focused automated tests do not provide real-browser validation, production security assurance, or full regression coverage.

## 12. Files Created / Modified (Implementation Evidence)

### Created
- `app/Services/PanelAccessRegistry.php`
- `app/Http/Controllers/Auth/SchoolGatewayAuthController.php`
- `resources/views/auth/school-gateway-login.blade.php`
- `resources/views/auth/school-gateway-select-panel.blade.php`
- `resources/views/auth/school-gateway-no-access.blade.php`
- `tests/Feature/Auth/SchoolGatewayAuthenticationTest.php`
- `tests/Feature/Api/ApiTenantIsolationTest.php` (API tenant-isolation regression coverage; currently untracked in working tree)

### Modified
- `app/Models/User.php`
- `routes/tenant.php`
- `app/Domains/School/Presentation/Http/SchoolAccessPortalController.php`
- `resources/views/school-access/portal.blade.php`
- `tests/Feature/Routing/SchoolAccessPortalTest.php`
- `lang/fr.json` (gateway-related FR translation keys; file re-encoded by JSON pretty-print)
- `app/Domains/Api/Services/OAuthService.php` (OAuth tenant-binding remediation; in HEAD)
- `app/Domains/Api/Services/ApiTokenService.php` (token creation user/school binding guard; in HEAD)
- `tests/Feature/Api/OAuthApiTest.php` (OAuth tenant-binding regression coverage; in HEAD)

### Preserved / not modified
- `docs/ADR/ADR-025.md` — remains **untracked** and **PROPOSED**.
- No governance status files changed by this documentation task.

## 13. Explicit Unresolved Risks (Not Fixed / Still Open)

| Risk | Status | Notes / next step |
|---|---|---|
| OAuth tenant/client/user binding vulnerability (original gap) | **Remediated for current OAuth flow; residual gaps remain** | Data/token-level tenant binding enforced in `OAuthService`/`ApiTokenService`; focused tests pass. No refresh-token flow exists today. API routes do not use tenant-host/domain resolution middleware; protection relies on token binding + resource-level ownership checks. Future API/domain endpoints must enforce equivalent scoping. |
| Session, remember-token, API-token, and permission-cache revocation | **Unresolved / not implemented** | Role/school/status changes have no centralized revocation/invalidation flow. Needs approved design covering sessions, remember-me tokens, API tokens, and Spatie permission cache. |
| MFA | **Not implemented** | Follow-up decision/implementation only; not part of this milestone. |
| Parent/Student relationship-based authorization | **Not implemented** | Family data must not be exposed without relation-based authorization and policy coverage. No Family portal is claimed as production-ready. |
| Audit persistence verification | **Limited** | Audit writes are best-effort and not asserted/monitored end-to-end in this pass. Add audit-row assertions/monitoring in approved follow-up. |
| Production cookie, trusted-host, proxy and rate-limit configuration | **Not production-validated** | Requires env/deployment hardening verification: secure/httponly/samesite cookies, trusted hosts/proxies, distributed rate-limit store, HTTPS. No wildcard cross-subdomain session cookie. |
| Real browser and mobile validation | **Not performed** | No real browser/E2E/mobile/device validation; no screenshots. |

## 14. Confirmation

- This documentation task updated documentation only: `docs/docs/00_Project_Management/ADR-025_School_Gateway_Implementation_Report.md`.
- **No application code or tests were modified by this documentation task.** `tests/Feature/Api/ApiTenantIsolationTest.php` was created in a prior verification step and remains an existing working-tree change; this documentation update did not modify it.
- No Git staging, commit, push, reset, stash, checkout, or clean performed.
- ADR-025 remains **PROPOSED** and untracked.
- No broader sprint or production authorization is implied.
