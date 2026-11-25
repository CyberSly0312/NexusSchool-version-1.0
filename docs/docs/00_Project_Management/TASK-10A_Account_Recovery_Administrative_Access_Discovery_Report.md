## 1. Executive Summary

This report is a read-only technical discovery for Task 10A. It establishes the current baseline for password management, account recovery, access revocation, School Admin lifecycle, role-assignment security, tenant isolation, audit/notification behavior, and test coverage. No application code, migration, test, configuration, or existing documentation was changed. No tests were executed; test files were inspected as source evidence only.

Evidence labels used below:

- **Confirmed** — directly established by inspected application/config/route/migration/vendor code or by existing test source.
- **Partially confirmed** — evidence establishes the design risk, but runtime exploitability or final behavior cannot be established statically.
- **Missing** — no implementation was found after explicit searches.
- **Potential risk** — requires runtime verification or a business decision before it can be classified as a defect.
- **Recommendation** — proposed remediation or implementation direction; nothing has been implemented.

### Highest-priority conclusions

| Area | Conclusion | Classification |
|---|---|---|
| Self-service password change | No authenticated user can currently change their own password through an application surface. Filament's stock profile page supports it, but no panel enables `->profile()`. | Confirmed missing functionality |
| Laravel password reset | `config/auth.php` and `password_reset_tokens` exist, but no forgot-password route, controller, view, mail, notification, or broker invocation exists. | Confirmed missing functionality |
| Administrative password reset | No secure tokenized reset exists. Super Admin and same-school School Admin can directly overwrite another user's password through generic Filament forms. Super Admin also has a legacy 24-hour one-time School Admin setup-link action. | Confirmed gap / confirmed policy inconsistency |
| Token storage | Laravel broker tokens would be hashed by vendor code if used; custom invitation and setup tokens are SHA-256 hashed. No plaintext password storage was found. | Confirmed implemented correctly for existing custom token flows |
| Session/token revocation | The application blocks inactive users reactively at panels, gateway, and API layers, but does not delete sessions, rotate `remember_token`, revoke API token rows, revoke invitations/setup tokens, or clear Spatie cache on deactivation. | Confirmed missing security control |
| School Admin departure | There is no supported Super Admin suspend/revoke/replace lifecycle. The former admin cannot be reliably identified, access cannot be fully revoked, and replacement provisioning is blocked while any School Admin remains or the email exists. | Confirmed missing lifecycle |
| RolesRelationManager | Task 08 creation allowlist (`teacher`, `secretary`, `accountant`) diverges from the relation manager's broader role list (`school_admin`, `director`, `student`, `parent`, etc.). The association callback has no allowlist, authorization, tenant check, or audit. | Confirmed policy inconsistency and authorization design defect |
| Crafted Livewire bypass | The callback uses an undefined Filament v5 method and the wrong state key, so exploitability as written is not statically proven. The missing server-side authorization remains a real design risk. | Partially confirmed; runtime verification required |
| Audit | Provisioning/setup/password-set and some auth events are audited. Role relation changes, status changes, direct password overwrites, and most revocation actions are not. | Confirmed audit gaps |
| Delivery | Mail defaults to `log`; `.env.example` uses `MAIL_MAILER=log`; production example contains placeholders. Links are delivered as Filament notifications that explicitly state no email or SMS was sent. | Confirmed; do not assume email/SMS |

### French-language requirement summary

All future user-facing text must be in French, using existing Francophone school terminology and project translation conventions. This includes navigation and menu labels, forms and field labels, validation messages, notifications and alerts, password-reset emails, confirmation dialogs, audit descriptions visible to users, and error messages. Technical identifiers, class names, and internal code comments may remain English.

Current evidence shows substantial French UI text in `resources/views/account-activation/activate.blade.php`, `resources/views/account-activation/activated.blade.php`, `resources/views/account-activation/invalid.blade.php`, `resources/views/school-admin/setup.blade.php`, `resources/views/school-admin/setup-done.blade.php`, `resources/views/auth/school-gateway-login.blade.php`, Filament resource labels, Super Admin notifications, `lang/fr.json`, `lang/fr/auth.php`, and `lang/fr/passwords.php`. However, `config/app.php` defaults `APP_LOCALE` and `APP_FALLBACK_LOCALE` to `en`, so future implementation must not assume that French is automatically active through the framework locale.

## 2. Initial Git Working Tree

Command executed before discovery:

```text
git status --porcelain=v1 -uall
```

Exact output:

```text
?? docs/docs/00_Project_Management/TASK-09_Student_Guardian_Account_Provisioning_Discovery_Report.md
```

The pre-existing untracked Task 09 report was preserved and treated as user work. No staging, commit, push, reset, checkout, clean, restore, stash, migration, seed, or database operation was performed.

## 3. Existing Password Management Architecture

### 3.1 Laravel authentication and password broker configuration

| Path | Symbol/config | Observed behavior | Evidence | Impact | Recommended remediation |
|---|---|---|---|---|---|
| `config/auth.php` | `defaults.guard`, `defaults.passwords`, `guards.web`, `passwords.users` | Web guard uses the session driver and `App\Models\User`; broker `users` uses table `password_reset_tokens`, `expire = 60` minutes, `throttle = 60` seconds; `password_timeout = 10800`. | Config | Broker scaffolding exists but is unreachable because no application reset flow was found. | Either implement a complete broker-backed reset flow or remove/align dormant configuration. |
| `database/migrations/0001_01_01_000000_create_users_table.php` | `password_reset_tokens` table | Columns: `email` primary key, `token` string, `created_at`. The column type is generic; hashing is performed by Laravel at runtime. | Migration | Compatible with Laravel's hashed broker tokens. | No schema change required for the broker itself. |
| `vendor/laravel/framework/src/Illuminate/Auth/Passwords/DatabaseTokenRepository.php` | `getPayload()`, `exists()`, `tokenExpired()`, `recentlyCreatedToken()` | Vendor code stores `$this->hasher->make($token)`, verifies with `Hash::check`, enforces expiry and throttle. | Vendor code | If the broker were reachable, tokens would be hashed, expiring, throttled, and deleted after reset. | Reuse this infrastructure instead of building a new token store for native reset. |
| `vendor/laravel/framework/src/Illuminate/Auth/Passwords/PasswordBroker.php` | `reset()`, `validateReset()` | Validates token, invokes the password-saving callback, and deletes the token. It does not change `status`, send mail, or invalidate sessions. | Vendor code | Any future reset flow must explicitly define status and session behavior. | Add application-level orchestration around broker reset. |
| `app/Models/User.php` | `App\Models\User` | Extends `Illuminate\Foundation\Auth\User`, which implements `CanResetPasswordContract` and uses `CanResetPassword`. | Code | Model is broker-compatible. | No model contract change needed. |
| `bootstrap/app.php`, `routes/web.php`, `routes/tenant.php`, `routes/verification.php` | Route registration | No `forgot-password`, `reset-password`, `password.reset`, `Password::broker()`, or `sendPasswordResetNotification()` usage was found. | Code/config | Confirmed: the broker is dead configuration. | Add routes/controller/action only if native reset is selected. |

### 3.2 Self-service password change

**Confirmed: no enabled self-service password-change flow exists.**

- All eight panels call `->login()` only:
  - `app/Providers/Filament/SuperAdminPanelProvider.php`
  - `app/Providers/Filament/SchoolAdminPanelProvider.php`
  - `app/Providers/Filament/TeacherPanelProvider.php`
  - `app/Providers/Filament/StudentPanelProvider.php`
  - `app/Providers/Filament/ParentPanelProvider.php`
  - `app/Providers/Filament/DirectorPanelProvider.php`
  - `app/Providers/Filament/SecretaryPanelProvider.php`
  - `app/Providers/Filament/AccountantPanelProvider.php`
- No panel enables `->profile()` or `->passwordReset()`.
- `vendor/filament/filament/routes/web.php` registers profile and password-reset routes only when the panel has those features enabled.
- `vendor/filament/filament/src/Auth/Pages/EditProfile.php` would provide current-password verification, `Password::default()`, confirmation, `Hash::make()`, and session password-hash update, but it is unreachable.
- No custom authenticated profile/account page or forced-password-change mechanism was found.

Therefore:

- Super Admin cannot change their own password through an application UI.
- School Admin cannot change their own password through an application UI.
- Director, Teacher, Secretary, Accountant, Student, and Parent cannot change their own passwords through an application UI.
- Password setting occurs only during Pending-account activation or legacy School Admin setup.

### 3.3 Login and logout flows

| Path | Symbol | Observed behavior | Evidence | Impact | Recommended remediation |
|---|---|---|---|---|---|
| `app/Http/Controllers/Auth/SchoolGatewayAuthController.php` | `login()`, `logout()`, `redirectAuthenticatedUser()`, `audit()` | Gateway validates email/password, calls `Auth::guard('web')->attempt(...)`, supports `remember`, regenerates session on login, audits success/failure/throttling, and on explicit logout calls `logout()`, `session()->invalidate()`, and `regenerateToken()`. Inactive users are logged out only when they hit a gateway route. | Code | Current-device cleanup works; other sessions are untouched. Gateway authenticates before checking `isActive()`. | Centralize status denial and revoke all devices on offboarding. |
| `vendor/filament/filament/src/Auth/Pages/Login.php` | Login flow | Uses `attemptWhen(..., fn ($user) => $user->canAccessPanel(...), $remember)`, then regenerates session. | Vendor code | Direct Filament login denies inactive/unauthorized users before session establishment. | Keep; align gateway semantics. |
| `vendor/filament/filament/src/Auth/Http/Controllers/LogoutController.php` | `__invoke()` | Logs out, invalidates session, regenerates CSRF token. | Vendor code | Current-device logout is correct. | Keep; add global revocation separately. |
| `app/Services/PanelAccessRegistry.php`, `app/Models/User.php` | `canUserAccessPanel()`, `canAccessPanel()` | Requires `UserStatus::Active`, role/panel allowlist, and active school for non-super-admin users. | Code | Panel access is fail-closed per request. | Keep as reactive defense; it is not session revocation. |
| `app/Providers/Filament/*PanelProvider.php` | Middleware stacks | Panels include `AuthenticateSession` and Filament `Authenticate`. | Code | Password-hash changes cause stale panel sessions to be rejected on the next panel request. | Add explicit invalidation at the moment of password change. |

### 3.4 Password validation and hashing

| Path | Symbol | Observed behavior | Evidence | Impact | Recommended remediation |
|---|---|---|---|---|---|
| `app/Domains/User/Rules/UserRules.php` | `create()`, `update()` | Domain rules require `required|string|min:8|confirmed` on create and `sometimes|nullable|string|min:8|confirmed` on update. | Code | Intended minimum is 8 with confirmation. | Use this as the shared baseline unless a stronger rule is approved. |
| `app/Domains/User/Services/UserService.php` | `create()`, `update()`, `toggleStatus()` | Hashes passwords with `Hash::make()`; unset password on empty update; status changes only update fields and dispatch `UserUpdated`. | Code | Domain service hashes safely but has no revocation or audit side effects. | Extend status/password transitions through a lifecycle service. |
| `app/Models/User.php` | `casts()` | `password` uses the `hashed` cast. | Code | Plain values assigned to `password` are hashed automatically; already-hashed values are not double-hashed. | Keep. |
| `app/Domains/User/Services/AccountProvisioningService.php` | `provision()` | Creates Pending users with `Hash::make(Str::random(64))` and issues an invitation. | Code | Initial password is unknown to both operator and user. | Keep as the safe provisioning baseline. |
| `app/Domains/User/Presentation/Http/AccountInvitationActivationController.php`, `app/Domains/User/Services/AccountInvitationActivationService.php` | Activation validation | Requires password confirmation and minimum 8 characters. | Code/test source | Safe baseline for generic activation. | Align with final password policy decision. |
| `app/Domains/School/Presentation/Http/SchoolAdminSetupController.php` | `store()` | Requires `Password::min(12)->letters()->mixedCase()->numbers()->symbols()` plus confirmation. | Code/test source | Stronger than all other flows. | Decide whether this should be the universal policy or School Admin-only. |
| `app/Filament/Resources/UserResource.php` | `form()` password section | Hashes with `Hash::make()`, requires confirmation via `same('password')`, and sets `maxLength(255)`, but has no explicit `min:8`. | Code | Confirmed inconsistency: generic Filament admin overwrite can choose a password shorter than domain rules. | Reuse `UserRules` or a shared password value object/rule. |
| `app/Filament/Resources/CentralUserResource.php` | `form()` | Uses `bcrypt($state)` via `dehydrated()`; create is `required`; no confirmation or minimum. Also uses `name`, while the users schema/model use `first_name`/`last_name`. | Code | Inconsistent policy and likely broken field mapping. | Fix field mapping and apply shared rules. |

### 3.5 Administrative password-management interfaces

| Interface | Path | Behavior | Classification |
|---|---|---|---|
| School Admin staff creation | `app/Filament/Resources/UserResource.php::schoolAdminProvisioningForm()`, `app/Filament/Resources/UserResource/Pages/CreateUser.php`, `app/Domains/User/Services/AccountProvisioningService.php` | No password field; creates Pending user with random password and 48-hour invitation. Role limited to `teacher`, `secretary`, `accountant`. | Correct secure provisioning pattern |
| Generic user edit in school-admin panel | `app/Filament/Resources/UserResource.php::form()`, `app/Filament/Resources/UserResource/Pages/EditUser.php`, `app/Policies/UserPolicy.php` | Same-school School Admin can edit an existing user and overwrite their password directly. No current-password check, token, expiry, or explicit audit. | Confirmed policy/security gap |
| Central user management in Super Admin panel | `app/Filament/Resources/CentralUserResource.php`, `app/Filament/Resources/CentralUserResource/Pages/EditCentralUser.php` | Direct password field; no reset link; no confirmation/minimum; potential schema mismatch. | Confirmed gap |
| Super Admin school edit page | `app/Filament/Resources/SuperAdminSchoolResource/Pages/EditSuperAdminSchool.php` | Legacy `issue_school_admin_setup_link` action issues a one-time 24-hour hashed setup link; notification shows the link to the operator. | Partial recovery capability |

### 3.6 Explicit absence findings

No implementation was found for:

- Laravel/Filament forgot-password or reset-password routes.
- Custom password-reset controller, action, listener, notification, mailable, or Blade mail view.
- `Password::broker()` or `sendPasswordResetNotification()` invocation.
- Enabled Filament `->profile()` in any panel.
- Custom authenticated profile/account page.
- Forced password change after login.
- First-login forced change distinct from invitation/setup activation.
- Password history, rotation, or account-password expiry.
- Administrative “send reset link” endpoint based on `UserInvitation`.
- Email or SMS delivery of activation/setup/reset links.

## 4. Password Reset and Recovery Lifecycle

### 4.1 Native Laravel reset lifecycle — configured but unreachable

If Laravel's broker were invoked, the observed behavior would be:

1. `Password::broker('users')->sendResetLink(...)` writes a hashed token to `password_reset_tokens`.
2. `expire = 60` minutes and `throttle = 60` seconds apply from `config/auth.php`.
3. `PasswordBroker::reset()` validates the token, saves the new password through the user callback, and deletes the token.
4. `DatabaseTokenRepository` hashes tokens with the application hasher; plaintext tokens are not stored.

However, no route, controller, view, notification, mailable, or call into the broker exists. Therefore:

- **Are broker reset tokens hashed?** Yes, by vendor code, but no reset flow is reachable.
- **What is broker expiry/single-use behavior?** 60-minute expiry, 60-second throttle, token deleted after successful reset, but unreachable.
- **Does broker reset activate Pending/Suspended accounts?** No; the broker only saves password and deletes the token. No application code changes `status`. In the current repository it cannot run at all.
- **Are sessions invalidated after broker reset?** No explicit application-level invalidation exists. Filament `AuthenticateSession` would reject stale sessions on the next panel request because the stored password hash changes; the school gateway does not use that middleware.

### 4.2 Generic invitation activation

| Path | Symbol/config | Observed behavior | Evidence | Impact | Recommended remediation |
|---|---|---|---|---|---|
| `routes/tenant.php` | `school.account.activation.show`, `school.account.activation.store` | Public tenant-host routes throttled by named `invitation-activation` limiter; no authentication, auto-login, or API token issuance. | Code/route | Safe first-time setup surface. | Reuse for reset/replacement purposes only with explicit guards. |
| `app/Domains/User/UserServiceProvider.php` | `invitation-activation` limiter | 60/minute by IP and 10/minute by hashed token. | Code | Rate limiting exists. | Keep and extend to any reset flow. |
| `app/Models/UserInvitation.php` | `EXPIRATION_HOURS = 48`, `issue()`, `consume()`, `revoke()`, `hashToken()`, `findUsableByPlainToken()` | Tokens are `bin2hex(random_bytes(32))`; only SHA-256 hash is stored; prior usable invitations for the same user/school/purpose are revoked; `consume()` locks the row; usable means not consumed, revoked, or expired. | Code/migration/test source | Hashed, expiring, single-use, revocable. | Extend with explicit reset purpose rather than inventing a token system. |
| `app/Domains/User/Services/AccountInvitationActivationService.php` | `activate()`, `assertInvitationIsActivatable()`, `assertUserIsActivatable()`, `assertExpectedSchoolMatches()` | Hashes submitted token, locks rows, checks purpose/state/school, requires `UserStatus::Pending`, sets password hash and `status = Active`, then consumes invitation. | Code/test source | Pending users can activate; Suspended, Inactive, and Active users are rejected. | Keep the Pending-only guard for initial activation. |
| `app/Domains/User/Presentation/Http/AccountInvitationActivationController.php` | `show()`, `store()` | Shows French activation form; never authenticates the user. | Code | No session is created by activation. | Keep unless product explicitly wants auto-login. |

Existing test source confirms: expired, consumed, revoked, wrong-school, wrong-purpose, mismatched-password, and short-password invitations are rejected; activation does not issue an API token or log the user in.

### 4.3 Legacy School Admin setup/recovery link

| Path | Symbol/config | Observed behavior | Evidence | Impact | Recommended remediation |
|---|---|---|---|---|---|
| `app/Domains/School/Services/SchoolAdminProvisioningService.php` | `TOKEN_TTL_MINUTES = 60 * 24`, `issueSetupLink()`, `findValidByToken()`, `redeem()`, `createSetupToken()`, `findRecoverableSchoolAdmin()` | One-time setup token valid 24 hours; SHA-256 hash stored; prior unused links deleted; token bound to school/user/role; redemption sets password, marks token used, deletes sibling links, and audits `PasswordSet`; Suspended users are rejected. | Code/test source | Strong token mechanics but tied to legacy setup semantics. | Generalize behind an explicit audited reset/replacement service. |
| `app/Domains/School/Presentation/Http/SchoolAdminSetupController.php` | `show()`, `store()`, `resolveToken()` | Public, tenant-bound, throttled setup controller; verifies valid token, school match, and non-Suspended user; enforces strong 12-character password policy. | Code | Password is changed only on redemption, not on link issuance. | Reuse policy but clarify actor authorization and purpose. |
| `routes/tenant.php` | `school.admin.setup.*` | Setup routes are behind `InitializeTenancyForSchoolSetup` and `throttle:30,1`. | Route | Token is tenant-host-bound. | Keep tenant binding. |
| `app/Domains/School/Models/SchoolAdminSetupToken.php` | `isValid()`, `scopeValid()` | Validity based on used/expired state. | Code | Clear model-level token semantics. | Keep or consolidate with `UserInvitation`. |

Important limitation: `SchoolAdminProvisioningService::findRecoverableSchoolAdmin()` excludes only Suspended admins and may include Inactive or Pending admins. Redemption preserves non-Pending status, so an Inactive former admin can be selected for a recovery link but remains inactive after redemption. This is a confusing lifecycle state and should be resolved before reuse.

### 4.4 Answers to the required password-recovery questions

| Question | Answer |
|---|---|
| Which users can change their own passwords? | None through an enabled application UI. Passwords can be set only during invitation activation or School Admin setup. |
| Who can initiate a password reset for another user? | No one through a Laravel/Filament reset flow. Super Admin can issue a legacy setup link for a recoverable School Admin. Super Admin and same-school School Admin can directly overwrite passwords through generic Filament forms. |
| Does the application support administrative password resets? | Not as a secure tokenized reset. It supports direct administrative overwrite and a legacy School Admin setup-link mechanism. |
| Are reset tokens hashed or plaintext? | Laravel broker tokens would be hashed by vendor code. Custom `UserInvitation` and `SchoolAdminSetupToken` tokens are SHA-256 hashed. Plaintext is returned only in URLs/notifications/DTOs, not persisted. |
| What is token expiration/single-use behavior? | Broker: 60 minutes and deleted after reset, but unreachable. Invitations: 48 hours, `consumed_at`, `revoked_at`. School Admin setup links: 24 hours, `used_at`, sibling links deleted. |
| Are sessions invalidated after password change/reset? | No explicit application-level invalidation. Filament panel sessions are reactively rejected on the next request through `AuthenticateSession`; gateway routes are not covered. Direct admin overwrite has no explicit invalidation. |
| Can a password reset accidentally activate Pending or Suspended accounts? | No reset flow exists. Activation activates only Pending users and rejects Suspended users. Setup redemption rejects Suspended users and does not reactivate Inactive users. |
| Are there inconsistencies between Filament panels and Laravel auth? | Yes: broker configured but unreachable; panels expose no profile/reset; gateway checks status after `attempt()` while Filament checks via `attemptWhen()`; password policy differs across domain, activation, setup, generic Filament form, and central resource. |

## 5. Session and Token Revocation Analysis

### 5.1 Web sessions and remember-me

| Path | Symbol/config | Observed behavior | Evidence | Impact | Recommended remediation |
|---|---|---|---|---|---|
| `config/session.php`, `.env.example` | `SESSION_DRIVER=database`, table `sessions`, lifetime `120`, `expire_on_close=false` | Sessions are stored in the database and can survive for up to 120 idle minutes and across browser close. | Config | Deactivated users retain stored sessions until explicit current-device logout or expiry. | Delete session rows for the user during revocation. |
| `database/migrations/0001_01_01_000000_create_users_table.php` | `sessions`, `remember_token` columns | `sessions.user_id` is nullable and indexed; no FK/cascade. `users.remember_token` exists. | Migration | Status changes do not automatically touch sessions or recaller tokens. | Use a dedicated revocation service; do not rely on cascades. |
| `app/Domains/User/Enums/UserStatus.php`, `app/Models/User.php` | `Active`, `Inactive`, `Pending`, `Suspended`, `isActive()` etc. | Status is data, not an authentication event. | Code | Changing status does not log the user out. | Treat transition to Inactive/Suspended as a security event. |
| `app/Domains/User/Services/UserService.php`, `app/Filament/Resources/UserResource.php` | `update()`, `toggleStatus()`, `status` select | Status can be changed in the database/UI with no session deletion, remember-token rotation, token revocation, invitation revocation, or dedicated audit. | Code | Confirmed missing revocation. | Route all status transitions through a lifecycle service/observer. |
| `vendor/laravel/framework/src/Illuminate/Auth/SessionGuard.php` | `user()`, `logout()`, `logoutCurrentDevice()`, `cycleRememberToken()` | `logout()` cycles the remember token; `logoutCurrentDevice()` does not. Deactivation code calls neither for other devices. | Vendor code | Old remember-me cookies remain valid until a gated surface handles them. | Rotate/null `remember_token` on revocation. |
| `app/Http/Controllers/Auth/SchoolGatewayAuthController.php` | `redirectAuthenticatedUser()`, `logout()` | Inactive users hitting the gateway are logged out and their current session invalidated; other sessions remain. | Code | Current-device cleanup only. | Central revocation. |
| Filament panel middleware | `Authenticate`, `AuthenticateSession` | Panel requests call `canAccessPanel()` and compare password hash; non-active users receive denial and stale sessions are flushed on next request. | Code/vendor | Reactive denial is strong, but the session may persist until that request happens. | Keep, but add immediate deletion. |

**Conclusion:** the application cannot currently revoke all web access belonging to a user. It can prevent new/continued authorized panel, gateway, and API access reactively, but stored sessions and remember-me credentials remain.

### 5.2 API and OAuth tokens

| Path | Symbol | Observed behavior | Evidence | Impact | Recommended remediation |
|---|---|---|---|---|---|
| `app/Domains/Api/Services/OAuthService.php` | `issueToken()`, `validateToken()`, `revokeToken()`, `revokeAllForClient()` | Issues custom SHA-256-hashed bearer tokens. Validation checks client active, school active, token/client/user school consistency, `! $user->isActive()`, `! $user->hasActiveSchool()`, and tenant match. No `revokeAllForUser()`. | Code | Inactive users and schools are blocked immediately with 401, but token rows remain active. Reactivation can restore old tokens until expiry. | Add `revokeAllForUser()` and call it from offboarding. |
| `app/Http/Middleware/ValidateApiToken.php` | `handle()` | Hashes bearer token, delegates to `OAuthService`, returns 401/403, injects `api_token`, `api_client`, `api_user`, and sets abilities header. | Code | Strong reactive API enforcement. | Keep; add central revocation. |
| `app/Domains/Api/Services/ApiTokenService.php` | `create()`, `revoke()` | `create()` checks active user and same school; `revoke()` soft-deletes one token and dispatches `ApiTokenRevoked`. | Code | Single-token revocation exists; bulk user revocation does not. | Add bulk user revocation using the same soft-delete/event path. |
| `app/Models/ApiToken.php`, `database/migrations/2026_07_16_000052_create_api_tokens_table.php` | Token schema | ULID PK, `token` hash, abilities JSON, `expires_at`, `is_active`, `SoftDeletes`, school/client scoping, nullable `user_id` with no FK. | Code/migration | Soft delete preserves history; no automatic cleanup on user status change. | Keep soft deletes; add index if bulk lookups become common. |
| `app/Jobs/PruneExpiredTokens.php`, `bootstrap/app.php` | `PruneExpiredTokens` | Job soft-deletes expired tokens but is not scheduled in the observed scheduler configuration. | Code/config | Stale rows accumulate; expiry is still enforced during validation. | Schedule pruning if desired. |
| `composer.json`, `config/sanctum.php`, `routes/api.php` | Sanctum/Passport surface | Sanctum config and personal-access-token migration exist, but no `HasApiTokens`, `createToken()`, `auth:sanctum`, Passport, refresh-token, or provider-grant implementation was found. | Code/config/grep absence | No additional token surface requires revocation today. | Do not add Sanctum/Passport without integrating them into the same revocation service. |

### 5.3 Spatie Permission cache

- `config/permission.php` sets `events_enabled => false` and caches permissions for 24 hours under `spatie.permission.cache`.
- Cache clearing was found only in seeding/grant flows such as `database/seeders/PermissionSeeder.php`, `app/Domains/School/Services/SchoolAdminProvisioningService.php`, and `app/Domains/School/Services/SchoolAdminPermissionGrantService.php`.
- Deactivation and relation-manager role changes do not explicitly clear the cache.
- Current panel denial is based on `hasRole()`/`hasAnyRole()` plus active-status checks, so stale cache does not by itself grant access to inactive users.
- Role removal without cache clear may nevertheless leave stale authorization state for role checks that do not also check status.

**Recommendation:** call `PermissionRegistrar::forgetCachedPermissions()` after any role/status transition that changes authorization state.

### 5.4 Invitations and setup tokens during offboarding

- `app/Models/UserInvitation.php` provides `revoke()`, but no deactivation caller was found.
- Generic activation requires `UserStatus::Pending`, so an inactive former admin cannot use an old invitation to reactivate.
- `SchoolAdminProvisioningService::redeem()` rejects trashed/Suspended users but not Inactive users; it does not reactivate Inactive users.
- No deactivation-triggered deletion/revocation of unused `SchoolAdminSetupToken` rows was found.

**Recommendation:** revoke all usable invitations and unused setup tokens for defense in depth, even though current status guards reduce immediate exploitability.

### 5.5 School status changes

| Path | Symbol | Observed behavior | Evidence | Impact | Recommended remediation |
|---|---|---|---|---|---|
| `app/Http/Middleware/InitializeTenancyForPanel.php` | Tenant resolution/status check | Unknown hosts fail closed; inactive/suspended schools receive 403; authenticated users must match tenant school. | Code/test source | Access is blocked while the school is inactive. | Keep. |
| `app/Services/PanelAccessRegistry.php` | `canUserAccessPanel()` | Requires active school for non-super-admin panel access. | Code | Reactive denial. | Keep. |
| `app/Domains/Api/Services/OAuthService.php` | `validateToken()` | Rejects inactive/suspended school. | Code | API access is reactively blocked. | Keep. |
| `app/Domains/School/Services/SchoolService.php` | `update()`, `suspend()`, `reactivate()` | Updates school status and dispatches `SchoolUpdated`/`SchoolSuspended`; no session/token revocation. | Code | Existing sessions/tokens can resume if the school is reactivated. | Include school suspension in the same revocation service. |
| `app/Filament/Resources/SuperAdminSchoolResource/Pages/EditSuperAdminSchool.php` | Normal Filament save / `ForceDeleteAction` | Normal school save can bypass `SchoolService` events and audit; force delete is exposed. | Code | School status lifecycle is not reliably audited and force delete can damage history. | Route through audited service; restrict force delete. |

### 5.6 Exact effect of deactivating a School Admin today

1. Status is changed to `Inactive` or `Suspended` through `UserResource` or `UserService`.
2. Existing `sessions` rows are not deleted.
3. `remember_token` is not rotated or nulled.
4. On the next Filament panel request, `AuthenticateSession` passes because password hash is unchanged; `PanelAccessRegistry` denies with 403 and leaves the session stored.
5. On the next gateway request, the current session is logged out, invalidated, and regenerated; other devices remain.
6. On API calls, `OAuthService::validateToken()` returns 401 because the user is inactive; `api_tokens` rows remain active.
7. Invitations/setup tokens are not explicitly revoked, but current status guards prevent activation/reactivation for inactive users.
8. Spatie permission cache is not explicitly cleared.
9. A replacement cannot be provisioned through existing guards while the former admin's role and email remain.

## 6. School Admin Departure and Replacement Lifecycle

### 6.1 Capability assessment

| Required capability | Current state | Evidence | Impact | Recommended remediation |
|---|---|---|---|---|
| 1. Identify active School Admin | **Partial / not reliable.** `EditSuperAdminSchool` has no admin identity/status display. `findSchoolAdmin()` returns the first non-trashed admin of any status; `findRecoverableSchoolAdmin()` excludes only Suspended. | `app/Filament/Resources/SuperAdminSchoolResource/Pages/EditSuperAdminSchool.php`, `app/Domains/School/Services/SchoolAdminProvisioningService.php` | Super Admin cannot reliably know which account is active. | Define canonical active admin relation/display identity, status, email, invitation state, last login, and recovery state. |
| 2. Initiate secure password reset | **Partial.** Legacy one-time 24-hour setup link exists; no invitation-based reset purpose. Direct password overwrite also exists. | `SchoolAdminProvisioningService::issueSetupLink()`, `EditSuperAdminSchool::issue_school_admin_setup_link`, `UserResource` | Recovery exists but not as a complete, audited reset lifecycle with actor/target invariants. | Add explicit reset purpose and service; remove direct password overwrite. |
| 3. Suspend/deactivate admin | **No safe UI/service.** Super Admin panel has `CentralUserResource` with no status control; `UserResource` status field is registered in the school-admin panel. | `app/Filament/Resources/CentralUserResource.php`, `app/Providers/Filament/SuperAdminPanelProvider.php`, `app/Providers/Filament/SchoolAdminPanelProvider.php` | Super Admin has no supported suspension workflow. | Add audited suspend/deactivate action and audit action. |
| 4. Revoke existing access | **Partial / unsafe.** Invitation revocation helper exists; role removal and delete/force-delete paths are ad hoc; no session/token/remember-token revocation. | `UserInvitation::revoke()`, `RolesRelationManager`, `CentralUserResource/Pages/EditCentralUser.php`, `EditSuperAdminSchool.php` | Cannot reliably cut off all access. | Implement transactional `UserAccessRevocationService`. |
| 5. Preserve historical records/ownership | **Partial.** `User` uses `SoftDeletes`, but force-delete/hard-delete cascades exist and audit actions are missing for lifecycle events. | `app/Models/User.php`, migrations, `app/Enums/AuditAction.php` (actual path: `app/Domains/System/Enums/AuditAction.php`), `EditSuperAdminSchool.php` | History can be lost through force deletion or incomplete auditing. | Forbid force delete while admin history exists; keep numeric references; add audit actions. |
| 6. Provision replacement | **No / blocked.** Initial provisioning rejects any existing School Admin; UI requires `schoolAdmin() === null`; email availability check includes soft-deleted users. | `InitialSchoolAdminProvisioningService.php:63-94`, `EditSuperAdminSchool.php:158-175`, `AccountProvisioningService.php:137-154` | Cannot preserve former admin row and provision a replacement with the same email. | Add replacement-purpose flow that retires old admin, archives old email, detaches old role, and issues a new invitation. |
| 7. Prevent multiple active School Admins | **Partial only.** Guards exist in provisioning services, but no global invariant and role assignment can bypass them. | `AccountProvisioningService.php`, `InitialSchoolAdminProvisioningService.php`, `RolesRelationManager.php`, `CentralUserResource.php` | Multiple `school_admin` roles can exist for one school through non-service paths. | Funnel every attach/detach through one service and enforce a canonical active-admin invariant. |

### 6.2 School deactivation effect

School suspension currently denies panel and API access reactively but does not:

- change `User.status`,
- delete sessions,
- rotate remember tokens,
- revoke `api_tokens`,
- revoke invitations/setup tokens,
- or reliably write an immutable audit record when school status is changed through the normal Filament save path.

Access can therefore resume if the school is reactivated. This must be decided explicitly: either school suspension should only block tenant access, or it should trigger the same revocation service as user suspension.

## 7. RolesRelationManager Security Audit

### 7.1 Overall verdict

**Task 08 comparison verdict: confirmed divergence.**

Task 08 provisioning allows only `teacher`, `secretary`, and `accountant` for School Admin staff creation. The existing-account role relation manager exposes `school_admin`, `director`, `student`, and `parent` in addition to those three roles, and its mutation callback contains no application-level authorization.

**Crafted Livewire bypass verdict: partially confirmed.**

The absence of server-side authorization is confirmed. However, the installed Filament v5 action API does not define `associateRelatedRecordUsing()`, and the callback reads `role_id` while Filament uses `recordId`. The current code is likely broken before the callback executes. Runtime exploitability as written cannot be confirmed statically. A runtime test must be added later, but must not be run during this discovery.

### 7.2 Detailed verification

| # | Verification item | Result | Exact evidence | Observed behavior | Impact | Recommended remediation |
|---:|---|---|---|---|---|---|
| 1 | Which roles School Admin can see/assign | Confirmed | `app/Filament/Resources/UserResource/RelationManagers/RolesRelationManager.php:24-32,44-60,91-100`; `tests/Feature/Filament/SchoolAdminStaffProvisioningTest.php:518-522` | Roles list: `school_admin`, `director`, `teacher`, `student`, `parent`, `accountant`, `secretary`. Test source asserts `school_admin` is visible. | Privileged roles are exposed outside Task 08's creation policy. | Use one shared allowlist for creation and assignment. |
| 2 | Server-side allowlist enforced | Confirmed missing | Callback at `RolesRelationManager.php:101-104`; contrast `AccountProvisioningService.php:22-33,156-214` | Creation has authoritative service validation; association directly calls `Role::findOrFail($data['role_id'])` and `$record->roles()->attach($role)`. | UI filtering is not an authorization boundary. | Revalidate role inside a domain/application service. |
| 3 | `associateRelatedRecordUsing()` validates role | Confirmed invalid callback | `RolesRelationManager.php:101-104`; `phpstan-baseline.neon:4953-4957`; `vendor/filament/actions/src/AssociateAction.php:48-86,289-329` | Undefined method in installed Filament v5; wrong state key (`role_id` vs `recordId`). | Current association path is broken; a naive fix would become insecure. | Use correct Filament relationship action/state key and authorize in callback/service. |
| 4 | School Admin can associate `school_admin`, `director`, `student`, `parent` | Confirmed at code/UI-intent level | `RolesRelationManager.php:24-32,91-104`; `app/Services/PanelAccessRegistry.php:63-105` | These roles are selectable in the school-admin role manager; roles drive panel access. | Potential privilege grant to another same-school account if action is corrected without guards. | Remove privileged roles from School Admin allowlist and enforce service-side. |
| 5 | Crafted Livewire bypass | Partially confirmed | `RolesRelationManager.php:93-104`; `vendor/filament/actions/src/AssociateAction.php`; `vendor/filament/forms/src/Components/Select.php:1733-1802` | No application-level submitted-role validation; `recordSelect()->options()` is used, not `recordSelectOptionsQuery()`; current method/key mismatch likely blocks execution. | Design is unsafe; exploitability needs runtime proof. | Add runtime regression test described below. |
| 6 | Dissociation/bulk dissociation authorized | Confirmed missing | `RolesRelationManager.php:106-113`; `vendor/filament/filament/src/Resources/RelationManagers/RelationManager.php:346-359` | `DissociateAction` and `DissociateBulkAction` have no role-specific authorization, last-admin guard, or audit. | Privileged roles, including last School Admin role, can potentially be removed. | Authorize every removal; enforce last-admin invariant. |
| 7 | Assignment/removal audited | Confirmed missing | `app/Providers/EventServiceProvider.php:261-262`; `config/permission.php:126`; `app/Domains/Role/Events/RoleAssignedToUser.php`, `RoleRevokedFromUser.php` | Custom role events have empty listener arrays; Spatie events disabled; relation manager bypasses `RoleService`. | Privilege changes are untraceable. | Enable/listen to role events or write `AuditLog` from `RoleService`. |
| 8 | Tenant boundaries enforced during association | Confirmed only at resource layer | `app/Filament/Resources/UserResource.php:236-252`; `tests/Feature/Routing/SecurityBaselineTest.php:159-200` | `UserResource::getEloquentQuery()` scopes School Admin users to `tenant()?->id`, failing closed with `1 = 0`; tests confirm cross-tenant direct edit returns 404. Relation manager has no explicit tenant guard. | Cross-tenant risk is reduced but lacks defense in depth. | Re-check actor school and target school inside role mutation service. |
| 9 | Super Admin/School Admin separation | Confirmed at panel layer | `PanelAccessRegistry.php:63-105`; `app/Policies/UserPolicy.php`; `app/Providers/Filament/SchoolAdminPanelProvider.php`; `app/Providers/Filament/SuperAdminPanelProvider.php` | Panels are role/host separated; `super_admin` is central-only; school admin update/delete limited to same school. Central resource has its own unrestricted roles select. | Separation is clear at panel level but role changes can alter future panel access. | Apply the same role policy to every role mutation entry point. |

### 7.3 Additional observations

- `RolesRelationManager::canView(Model $record)` always returns `true`. In Filament v5, relation-manager visibility is generally driven by `canViewForRecord()`; this method should not be relied on as a guard.
- `Actions\CreateAction` is hidden in the school-admin panel but is available elsewhere; `AssociateAction` is not hidden for School Admin.
- No test was found that submits crafted Livewire payloads to the relation manager.
- No last-active-School-Admin removal test or rule was found.

### 7.4 Exact future runtime test specification — do not add or run now

Add later as:

```text
tests/Feature/Filament/SchoolAdminRoleAssignmentSecurityTest.php
```

Proposed test:

```text
school_admin_role_assignment_is_allowlisted_authorized_audited_and_tenant_safe
```

Setup:

1. Create School A and School B.
2. Seed `super_admin`, `school_admin`, `director`, `teacher`, `student`, `parent`, `accountant`, `secretary`.
3. Create an active School Admin in School A.
4. Create a target School A user with only `teacher`.
5. Create a target School B user.
6. Set current panel to `school-admin` and initialize School A tenancy.
7. Mount `RolesRelationManager` with the School A target as owner and `EditUser::class` as page class.

Assertions:

- Relation manager mounts without `BadMethodCallException`.
- Approved roles `teacher`, `secretary`, and `accountant` can be assigned if product confirms this allowlist.
- Crafted association payloads using both `recordId` and `role_id` for `school_admin`, `director`, `student`, `parent`, and `super_admin` fail.
- `model_has_roles` is unchanged after every rejected attempt.
- School A admin cannot mount the manager/edit page for a School B target.
- `DissociateAction` and `DissociateBulkAction` cannot remove the last active `school_admin`.
- Every accepted and rejected privileged transition writes an `AuditLog` row with actor, target, role, school, outcome, and timestamp.

Expected result against current code: failure due to invalid action method/key mismatch, broad role exposure, missing authorization, missing last-admin guard, and missing audit entries.

## 8. Tenant Isolation and Authorization Analysis

| Surface | Path | Observed behavior | Evidence | Assessment |
|---|---|---|---|---|
| School-admin user query | `app/Filament/Resources/UserResource.php:236-252` | Scopes to `tenant()?->id`; fails closed with `whereRaw('1 = 0')` if no tenant. | Code/test source | Confirmed strong resource-layer isolation. |
| Direct user edit | `tests/Feature/Routing/SecurityBaselineTest.php:159-200` | Own-school edit succeeds; foreign-school direct URL returns 404; query contains no foreign users. | Test source | Confirmed by existing test source; not executed in this discovery. |
| Tenant host resolution | `app/Http/Middleware/InitializeTenancyForPanel.php`, `app/Http/Middleware/EnsureCentralDomain.php` | Unknown hosts fail closed; central domains do not serve school panels; forwarded host cannot select tenant. | Code/test source | Confirmed host-based tenant boundary. |
| Panel access | `app/Services/PanelAccessRegistry.php`, `app/Models/User.php` | Active status, role/panel mapping, and active school are required. | Code/test source | Confirmed role/panel matrix. |
| Invitation activation | `AccountInvitationActivationService.php`, `AccountInvitationActivationController.php`, `routes/tenant.php` | Invitation school must match resolved tenant school; central/unknown hosts fail closed. | Code/test source | Confirmed tenant binding. |
| School Admin setup | `SchoolAdminSetupController.php`, `InitializeTenancyForSchoolSetup.php` | Setup token must match tenant school; inactive/suspended schools are denied. | Code/test source | Confirmed tenant binding. |
| API validation | `OAuthService.php`, `ValidateApiToken.php` | Token, client, user, and school must share consistent school context; inactive school/user blocked. | Code/test source | Confirmed reactive tenant and status checks. |
| RolesRelationManager | `RolesRelationManager.php` | No explicit actor/target school equality check inside association callback. | Code | Confirmed lack of defense in depth; current resource query and middleware prevent most cross-tenant targets. |
| Central user management | `CentralUserResource.php` | Super Admin can manage users centrally and has an unrestricted roles select. | Code | Intended platform separation; must be included in any global role policy. |
| School Admin status control | `UserResource.php`, panel providers | Status field exists in school-admin panel, not in Super Admin `CentralUserResource`. | Code | Confirmed panel capability mismatch. |

**Overall assessment:** tenant isolation is strong at query, host, and middleware layers. The priority gap is not broad cross-tenant data leakage; it is missing service-level authorization and audit in the role mutation surface and missing Super Admin lifecycle controls.

## 9. Existing Audit and Notification Mechanisms

### 9.1 Audit infrastructure

| Path | Symbol | Observed behavior | Evidence | Impact | Recommended remediation |
|---|---|---|---|---|---|
| `app/Domains/System/Services/AuditService.php` | `record()` | Writes `AuditLog` and dispatches `AuditRecorded`. | Code | Central audit primitive exists. | Reuse for all lifecycle/revocation events. |
| `app/Models/AuditLog.php`, `database/migrations/2026_07_17_000070_create_audit_logs_table.php` | Audit schema | Stores action, actor, auditable, school, old/new values, timestamps. | Code/migration | Suitable for immutable administrative history. | Enforce immutability and tenant scoping in new events. |
| `app/Domains/System/Enums/AuditAction.php` | `Provisioned`, `SetupLinkIssued`, `PasswordSet`, etc. | Provisioning and setup actions are represented. | Code | Existing actions can support recovery. | Add `Suspended`, `Deactivated`, `AccessRevoked`, `PasswordResetRequested`, `PasswordResetCompleted`, `RoleChanged`, `SchoolStatusChanged`. |
| `app/Domains/School/Services/SchoolAdminProvisioningService.php` | Audit calls around provisioning/recovery/redemption | Audits provisioning, setup-link issuance, and password set without credentials. | Code/test source | Good precedent. | Mirror for reset and revocation. |
| `app/Domains/School/Services/InitialSchoolAdminProvisioningService.php` | `provision()` | Audits initial provisioning without password/token/URL. | Code/test source | Good precedent. | Extend to replacement. |
| `app/Http/Controllers/Auth/SchoolGatewayAuthController.php` | `audit()` | Writes auth login/logout/failure/throttle/denial events. | Code | Auth events are covered. | Add lifecycle events for access revocation. |
| `app/Providers/EventServiceProvider.php` | Role event mapping | `RoleAssignedToUser` and `RoleRevokedFromUser` map to empty listener arrays. | Code | Role privilege changes are not audited. | Register audit listener or write from `RoleService`. |
| `config/permission.php` | `events_enabled => false` | Spatie model events are disabled. | Config | No automatic role-event audit. | Enable deliberately or rely on domain events. |
| `app/Domains/Api/Services/ApiTokenService.php`, `app/Domains/Api/Listeners/AuditLogger.php` | API revocation | `ApiTokenRevoked` dispatches listeners; inspected listener writes `Log::info`, not central `AuditLog`. | Code | API revocation history is weaker than central audit. | Add central audit for security-relevant API revocations. |
| `app/Filament/Resources/UserResource.php`, `app/Domains/User/Services/UserService.php` | Password/status edits | No `AuditLog` write found for direct password overwrite or status change. | Code | Confirmed audit gap. | Add audit before/after without password values. |
| `app/Domains/School/Services/SchoolService.php`, `EditSuperAdminSchool.php` | School status edits | Service dispatches events but normal Filament save may bypass them; no listeners found for `SchoolSuspended`/`SchoolUpdated`. | Code | School suspension is not reliably audited. | Route through service and register listeners. |

### 9.2 Notification and delivery configuration

| Path | Observed behavior | Evidence | Impact | Recommended remediation |
|---|---|---|---|---|
| `config/mail.php` | `default => env('MAIL_MAILER', 'log')`; SMTP/SES/Postmark/Resend/sendmail/log/array/failover/roundrobin definitions exist. | Config | Default fallback is not a real delivery provider. | Do not assume email delivery; confirm production transport. |
| `.env.example` | `MAIL_MAILER=log`; placeholders/null credentials. | Config template | Local/default delivery writes to log. | Add provider config only when approved. |
| `.env.production.example` | `MAIL_MAILER=smtp` with placeholders and empty credentials. | Config template | Production provider is not actually configured in the template. | Confirm real deployment values outside this read-only task. |
| `config/services.php` | Postmark/Resend/SES env keys only. | Config | No concrete provider configured in repository. | Keep credentials out of code/report. |
| `app/Filament/Resources/SuperAdminSchoolResource/Pages/EditSuperAdminSchool.php` | Activation/setup links shown as Filament notifications; activation notification explicitly says: `Aucun e-mail ni SMS n’a été envoyé.` | Code | Confirmed current delivery mechanism is operator-visible notification. | Decide secure handoff channel; never log plaintext passwords. |
| `app/Filament/Resources/UserResource/Pages/CreateUser.php` | Staff activation link shown as one-time Filament notification with the same no-email/SMS statement. | Code | Consistent with current delivery model. | Keep French wording if delivery model remains. |
| `app/Jobs/SendNotification.php`, `app/Domains/Communication/Services/NotificationService.php`, `app/Domains/Communication/Enums/NotificationChannel.php` | In-app delivery marked delivered; email/SMS/WhatsApp/push integrations are future comments only. | Code | No external channel dispatch found. | Implement provider-specific adapters only if required. |
| Static searches in `app/**/*.php` and `resources/views/**` | No `Mail::`, `Notification` facade, `->notify(`, `Mailable` class, or mail Blade view found. | Grep/glob absence | No password-reset mail implementation exists. | Add French reset email templates if email is selected. |
| `app/Domains/Communication/Enums/TemplateCategory.php`, `NotificationTemplateSeeder.php`, `database/seeders/NotificationTemplateSeeder.php` | `password_reset` category exists/seeded. | Code | Dormant category only; not an implemented reset flow. | Use it or remove misleading reference. |
| `lang/fr/passwords.php` | French reset messages exist. | Code | Dormant translations for a missing flow. | Reuse if native reset is implemented. |

### 9.3 Current French user-facing strings that must be preserved/localized

Examples found in code/views:

- Filament navigation/labels in `UserResource.php`: `Utilisateurs`, `Utilisateur`, `Compte`, `Mot de passe`, `Dernière connexion`, status labels `Actif`, `Inactif`, `En attente`, `Suspendu`.
- `RolesRelationManager.php`: `Rôles`, `Rôle`, `Attribuer un rôle`, `Lier un rôle existant`, `Retirer`, `Retirer la sélection`.
- `EditSuperAdminSchool.php`: `Créer l’administrateur de l’établissement`, `Créer le compte`, `Générer un lien de configuration`, `Création impossible`, `Administrateur déjà présent`, and the one-time link notifications including `Lien d’activation à usage unique valable 48 h`, `Transmettre ce lien de manière sécurisée`, `Aucun e-mail ni SMS n’a été envoyé.`, `Lien à usage unique valable 24 h : ...`.
- Activation/setup/gateway views: French form labels, hints, success, invalid-link, expired-link, and login messages.
- `lang/fr/auth.php` and `lang/fr/passwords.php`: French authentication/reset messages.

All future equivalents—navigation, forms, validation, notifications/alerts, reset emails, confirmation dialogs, visible audit descriptions, and errors—must be French.

## 10. Existing Tests and Verification Results

### 10.1 Verification status

**No tests were executed.** The relevant suites are database-backed feature tests and this task is explicitly read-only/static. Test files were inspected as source evidence only. Therefore, “confirms” below means the test source asserts that behavior when run; it does not mean this discovery executed the test.

### 10.2 Tests inspected and what they cover

| Test file | Coverage established by source |
|---|---|
| `tests/Feature/Routing/SchoolAdminProvisioningTest.php` | Pending admin creation, hashed setup token, no plaintext password, single-use/expired/weak-password handling, tenant-host binding, inactive/suspended school denial, post-setup panel login, recovery link not changing password before redemption, audit references without credentials. |
| `tests/Feature/Routing/SuperAdminInitialSchoolAdminProvisioningTest.php` | Canonical activation link, French form/action labels, hashed 48-hour token, one-time notification, no email/SMS statement, no credential in audit, injected school/role/status/password ignored, inactive/foreign actor rejection, duplicate/soft-deleted email rejection, rollback, token redaction in debug output. |
| `tests/Feature/Domains/User/AccountInvitationActivationHttpTest.php` | Public tenant-host activation routes, French activation page, password set, active status, token consumption, no auto-login/API token, wrong-host/invalid/expired/consumed/revoked rejection, password confirmation/minimum, inactive/suspended school denial. |
| `tests/Feature/Domains/User/AccountInvitationActivationServiceTest.php` | Activation state transition, hashed password, purpose allowlist, expired/revoked/consumed/wrong-school/wrong-user rejection, weak-password rejection, transaction rollback. |
| `tests/Feature/Auth/SchoolGatewayAuthenticationTest.php` | Gateway login, panel selection, cross-tenant denial, session regeneration/invalidation on login/logout, throttling. Does not prove password-change or suspension revocation. |
| `tests/Feature/Api/OAuthApiTest.php` | Token issue/revoke/current-user behavior, cross-tenant/client/school rejection. Does not prove revocation on password change or user/school suspension. |
| `tests/Feature/Routing/SecurityBaselineTest.php` | Own-school edit, cross-school direct URL denial, tenant query scoping, host fail-closed, forwarded-host rejection, role/panel matrix, active-school requirement. Does not cover role relation manager mutation security. |
| `tests/Feature/Filament/UserResourceTest.php` | Basic user list/create/edit/delete rendering and generic invitation-based creation. Does not prove secure password reset or revocation. |
| `tests/Feature/Filament/SchoolAdminStaffProvisioningTest.php` | French provisioning form, Task 08 role allowlist, injection resistance, hashed invitation token, rollback, and visible `school_admin` option in EditUser role manager. Does not test crafted role assignment or audit. |
| `tests/Unit/Jobs/PruneExpiredTokensTest.php` | Enumerated as related; not used as primary evidence for this report. |
| `tests/Unit/Domains/System/AuditServiceTest.php` | `AuditService::record()` DTO and pagination only. Does not prove event listeners, admin-action coverage, immutability, or tenant isolation. |
| `tests/Feature/Domains/Student/StudentAuditTest.php` | Audit rows for create/update/suspend/archive/restore/graduate and school isolation. Shows audit precedent outside this task's lifecycle. |
| `tests/Feature/Domains/Staff/StaffAuditTest.php`, `tests/Feature/Domains/Guardian/GuardianAuditTest.php`, `tests/Feature/Domains/Enrollment/EnrollmentAuditTest.php`, `tests/Feature/Domains/Timetable/TimetableAuditTest.php` | Domain lifecycle audit precedent. Not specific to password/admin revocation. |
| `tests/Feature/Routing/SuperAdminLoginTest.php` | Stock Filament Super Admin login form behavior and masked password field. |

### 10.3 Missing tests to introduce during Task 10

1. Self-service password change: current-password verification, confirmation, policy, hash update, French validation messages, stale-session behavior.
2. Password change/recovery revocation: other sessions removed, remember token changed, API tokens revoked if policy requires, audit written without password.
3. Native/admin reset: request and completion routes, hashed token, expiry, throttle, one-time use, actor authorization, status unchanged, tenant binding, French email/notification, no plaintext leakage.
4. User suspension/deactivation: sessions removed, remember token rotated, API tokens soft-deleted, invitations/setup tokens revoked, Spatie cache cleared, reactivation does not resurrect old tokens.
5. School suspension: sessions/tokens revoked if policy requires; setup/activation links rejected; audit written through service.
6. School Admin replacement: old admin retired, history preserved, old role detached, old email archived if required, new invitation issued, one-active invariant maintained.
7. RolesRelationManager security: exact runtime test specified in section 7.4.
8. Role/status/school audit immutability: centralized `AuditLog` rows rather than `Log::info` only.
9. API/OAuth revocation audit: bulk user revocation event and audit coverage.
10. Delivery: faked email/notification transport, no password in payload, failure logging/retry behavior, French content.
11. Locale: French assertions for all new UI, validation, notifications, emails, confirmations, and errors; decision test for default locale if French becomes mandatory.

## 11. Confirmed Gaps, Risks, and Technical Limitations

### 11.1 Confirmed security defects / missing controls

| Finding | Classification | Evidence | Impact | Recommended remediation |
|---|---|---|---|---|
| Same-school School Admin can overwrite another user's password through `UserResource` without a token, current-password check, or audit. | Confirmed policy/security gap | `UserResource.php:146-160`, `UserPolicy.php`, `SchoolAdminStaffProvisioningTest.php:469-496` source | Violates “School Admins do not arbitrarily reset other users' passwords”; no traceability. | Remove direct password field for non-self edits; add explicitly authorized, audited reset flow. |
| Deactivation does not revoke sessions or remember tokens. | Confirmed missing control | `UserService.php`, `UserResource.php`, `SessionGuard.php`, grep absence | Former admin's other devices remain authenticated until reactive checks or expiry. | Implement transactional access-revocation service. |
| API/OAuth token rows are not revoked on deactivation; reactivation can restore old tokens. | Confirmed missing control | `OAuthService.php`, `ApiTokenService.php` | Stale bearer tokens remain active until expiry. | Add `revokeAllForUser()` and call from offboarding/password reset. |
| Role assignment/removal has no application-level authorization or audit in `RolesRelationManager`. | Confirmed authorization design defect | `RolesRelationManager.php:91-113`, `EventServiceProvider.php:261-262`, `config/permission.php:126` | Privilege changes can be untraceable; last-admin removal not blocked. | Funnel through `RoleService`, authorize actor/target/role, write audit. |
| Force-delete paths can destroy historical records. | Confirmed risk | `EditSuperAdminSchool.php:116-118`, `EditCentralUser.php`, migration cascades | Conflicts with preserve-history rule. | Restrict force delete for entities with lifecycle/history records. |
| Super Admin has no status/suspend/revoke replacement workflow for School Admin. | Confirmed missing lifecycle | `CentralUserResource.php`, `EditSuperAdminSchool.php`, provisioning services | Departure scenario cannot be completed safely. | Add audited suspend/revoke/replace actions. |

### 11.2 Confirmed policy inconsistencies

- Task 08 creation allowlist vs `RolesRelationManager` broader role list.
- Domain `min:8` vs generic Filament admin form no minimum vs School Admin setup 12-character complexity vs central resource no confirmation/minimum.
- Gateway authenticates before status denial; Filament uses `attemptWhen()` with `canAccessPanel()`.
- Super Admin panel has no status control; school-admin panel does.
- Broker configured but no reset routes; `password_reset` notification category and French reset strings exist but are unused.
- `CentralUserResource` uses `name` while the users schema uses `first_name`/`last_name`.

### 11.3 Missing functionality

- Self-service password change for every role.
- Secure administrative password reset for School Admin by Super Admin.
- Complete session/token/invitation revocation on suspension/deactivation.
- School Admin identity/status display and replacement lifecycle.
- Global one-active-`school_admin` invariant.
- Audit actions/listeners for reset, revocation, status, role, and school lifecycle changes.
- Runtime regression tests for the role manager and revocation behavior.
- Confirmed external delivery channel and French reset email templates.

### 11.4 Potential risks requiring runtime verification

| Risk | Why not confirmed | Required verification |
|---|---|---|
| Crafted Livewire role-assignment bypass | Current callback uses undefined `associateRelatedRecordUsing()` and wrong `role_id` key; direct exploitability cannot be established statically. | Add and run `SchoolAdminRoleAssignmentSecurityTest` described in section 7.4 during implementation, not now. |
| `canView(Model $record)` behavior in relation manager | Filament v5 may use `canViewForRecord()` instead; static evidence does not prove this method is effective. | Runtime manager-mount test with privileged/non-privileged actors. |
| Stale Spatie permission cache causing unauthorized access | Panel access also checks active status and roles; whether a specific cached-role path is exploitable needs a targeted scenario. | Runtime test after role removal with cache warm. |
| Production mail delivery | Repository contains only examples/defaults; real environment was not inspected beyond redacted `APP_ENV`/`MAIL_MAILER`. | Confirm deployment configuration during implementation. |
| School suspension semantics | Product must decide whether suspension only blocks tenant access or also revokes stored credentials. | Business confirmation plus tests. |

### 11.5 Technical limitations of this discovery

- Static inspection only; no runtime attacks, no test execution, no database queries, no migrations.
- Live `.env` values were not disclosed; only non-secret environment names were observed.
- Vendor behavior was inspected from installed source, but no framework upgrade/downgrade was performed.
- Some subagent notes used shorthand paths; all critical paths in this report were checked against the repository structure. Correct examples include `app/Domains/User/Enums/UserStatus.php`, `app/Domains/System/Enums/AuditAction.php`, `app/Domains/Api/Services/OAuthService.php`, `app/Http/Controllers/Auth/SchoolGatewayAuthController.php`, and `app/Providers/Filament/*PanelProvider.php`.

## 12. Recommended Task 10 Implementation Slices

The following separable slices avoid unrelated authentication redesign, MFA, and cross-school identity work. They reuse existing services rather than adding unnecessary infrastructure. If security risk must drive priority, implement slices 5 and 3 first, then slices 1, 2, 4, and 6; do not add reset capability while role-assignment and revocation gaps remain open.

### Slice 1 — Self-service password change

- **Domain:** centralize password policy in a shared rule/value object; require current password, confirmation, and hashed persistence.
- **Application:** add a password-change application service that returns explicit success/failure and emits an auditable event without password values.
- **Infrastructure:** change the session password hash through the authenticated guard; decide whether API tokens are revoked on password change.
- **Presentation:** enable a French Filament profile page or add a French account page for all panels; use `lang/fr` keys for labels, validation, notifications, and errors.
- **Tests:** own-password change success, wrong current password, weak password, mismatch, French messages, stale sessions, token policy.

### Slice 2 — Secure administrative reset for School Admin

- **Domain:** define allowed actors (`Super Admin` only for `school_admin` target), target status rules, and reset purpose.
- **Application:** extend `UserInvitation` with a reset purpose and add a reset orchestration service; reuse hashed SHA-256 tokens, expiry, single-use consumption, tenant binding, and actor/target audit. Never set or transmit plaintext passwords.
- **Infrastructure:** use the supported delivery mechanism confirmed at implementation time. If no provider is configured, keep the existing secure one-time notification/handoff pattern and state it clearly in French; do not silently write reset links to logs in production.
- **Presentation:** add a Super Admin action on the school/admin record with French confirmation dialog, validation, notification, and error messages.
- **Tests:** actor authorization, target authorization, token hash/expiry/revocation/single-use, no status change, no password leakage, French delivery content.

### Slice 3 — Session and token revocation

- **Domain/application:** add `UserAccessRevocationService::revoke(User $user, ?User $actor, string $reason)` and dispatch `UserAccessRevoked`.
- **Infrastructure:** delete `sessions` rows, rotate/null `remember_token`, revoke all `ApiToken` rows through `ApiTokenService::revoke()`, revoke usable invitations and unused setup tokens, and clear Spatie permission cache.
- **Middleware:** keep reactive `PanelAccessRegistry`, gateway, and API checks; add immediate revocation at the status transition rather than relying on next-request denial.
- **Audit:** write central `AuditLog` with actor, reason, counts, and timestamp; never include secrets.
- **Tests:** all-device session invalidation, recaller rejection, token soft-delete, invitation/setup token revocation, cache clear, reactivation non-resurrection.

### Slice 4 — School Admin suspension and replacement lifecycle

- **Domain:** define canonical active School Admin per school and one-active invariant; define status semantics for departure (`Suspended` vs `Inactive`).
- **Application:** add lifecycle services for identify, suspend, revoke, preserve, and replace. Use transactions/locks to avoid duplicate admins.
- **Infrastructure/persistence:** preserve the former user row and numeric references; archive/change the old email only if business confirms; adjust provisioning guards to ignore retired/detached former admins.
- **Presentation:** show admin identity, email, status, last login, and recovery state in `SuperAdminSchoolResource`; add French suspend/revoke/replace actions with confirmations.
- **Safety:** restrict force delete while administrative history exists. Ensure school suspension uses the same revocation path if business confirms.

### Slice 5 — RolesRelationManager authorization and tenant hardening

- Replace direct `$record->roles()->attach()` with `RoleService` or a new authoritative role-assignment service.
- Create one role matrix reused by `AccountProvisioningService`, `UserResource`, `RolesRelationManager`, and `CentralUserResource`.
- Fix the Filament v5 action/state bug using the correct relationship action and `recordId` handling; add `recordSelectOptionsQuery()` or equivalent server-side ID scoping.
- Add explicit actor/target school checks, role allowlist checks, last-active-School-Admin protection, and dissociation/bulk-dissociation authorization.
- Write audit entries for accepted and rejected privileged transitions.
- Add the exact runtime test from section 7.4.

### Slice 6 — Audit coverage and regression suite

- Add missing `AuditAction` cases for password reset request/completion, suspension, deactivation, access revoked, role changed, and school status changed.
- Register role event listeners or write directly from `RoleService`; enable Spatie events only with a clear listener strategy.
- Route `UserResource`, `UserService`, `SchoolService`, and Super Admin school status changes through audited services.
- Run all new tests in an isolated test database with faked mail/notification transport.
- Assert French text for every new user-facing surface.

## 13. Business Rules Requiring Final Confirmation

| # | Question requiring confirmation | Current evidence/why it matters |
|---:|---|---|
| 1 | What is the supported reset-link delivery channel? | No email/SMS provider is configured in repository defaults; current links are Filament notifications and explicitly say no email/SMS was sent. |
| 2 | Should reset links be delivered by email in production, or remain a secure operator-to-user handoff? | Business rule says “supported delivery mechanism,” but no provider is verified. |
| 3 | Which statuses are eligible for administrative reset? | Current activation requires Pending; setup rejects Suspended and leaves Inactive inactive. Reset should probably target Active only, but this needs confirmation. |
| 4 | Can a reset occur during school suspension? | Activation/setup routes deny inactive/suspended schools; product must decide reset behavior. |
| 5 | Should a password change revoke all API tokens and other sessions? | Current system does neither explicitly; Filament only reacts on next request. |
| 6 | Should the current session survive a user's own password change? | Laravel/Filament profile behavior updates the stored hash; explicit policy is absent. |
| 7 | What is the universal password policy? | Existing rules range from 8 characters to 12 complex characters; generic admin forms have weaker/no rules. |
| 8 | May Super Admin reset passwords for roles other than School Admin? | Intended direction explicitly names School Admin; current generic UI effectively allows more. |
| 9 | What exactly may School Admin assign/remove via role manager? | Task 08 creation allows three staff roles, but role manager exposes more; final matrix must be explicit. |
| 10 | Is one active School Admin per school a hard invariant? | Current enforcement is partial and bypassable through role assignment. |
| 11 | How should the former admin's email be handled for replacement provisioning? | Email uniqueness includes soft-deleted users; preserving history and reusing email conflict today. |
| 12 | Should `Inactive` or `Suspended` be the canonical departure status? | Both are blocked by access checks, but lifecycle/audit semantics differ. |
| 13 | Should school suspension revoke stored credentials or only block tenant access? | Current behavior is reactive only; access resumes on reactivation. |
| 14 | Is force deletion ever allowed for schools/users with admin history? | Current UI exposes force delete; preserve-history rule suggests restriction. |
| 15 | Should French become the default application locale? | Current default is English despite substantial French UI. |
| 16 | Are user-visible audit descriptions required, or only internal audit records? | Any visible description must be French; confirmation affects translation scope. |

### Explicit French-language obligations for future implementation

Every new user-facing string must be French, including:

- navigation and menu labels;
- forms, field labels, placeholders, and help text;
- validation messages, including password rules and mismatch errors;
- notifications and alerts, including success, warning, danger, and persistent notifications;
- password-reset emails and any SMS/other-channel messages;
- confirmation dialogs and modal descriptions;
- audit descriptions visible to users;
- error and exception messages shown to users;
- status labels and role labels;
- expired/invalid/reset-link pages.

Use existing conventions in `lang/fr.json`, `lang/fr/auth.php`, `lang/fr/passwords.php`, French Blade views, and French Filament labels. Use Francophone school terminology such as `établissement`, `administrateur de l’établissement`, `enseignant`, `secrétaire`, `comptable`, `élève`, and `parent`. Technical identifiers, class names, and internal comments may remain English.

## 14. Exact Files Inspected

All paths are relative to the repository root. Vendor files are included where they were used to verify framework behavior. Test files were inspected as source only and were not executed.

### Application, domain, models, services, policies, events, listeners

- `app/Domains/Api/Enums/AuditAction.php`
- `app/Domains/Api/Events/ApiClientDisabled.php`
- `app/Domains/Api/Events/ApiTokenCreated.php`
- `app/Domains/Api/Events/ApiTokenRevoked.php`
- `app/Domains/Api/Listeners/AuditLogger.php`
- `app/Domains/Api/Listeners/CacheClearer.php`
- `app/Domains/Api/Listeners/NotificationDispatcher.php`
- `app/Domains/Api/Listeners/RetryDispatcher.php`
- `app/Domains/Api/Rules/ApiTokenRules.php`
- `app/Domains/Api/Services/ApiTokenService.php`
- `app/Domains/Api/Services/OAuthService.php`
- `app/Domains/Communication/Enums/TemplateCategory.php`
- `app/Domains/Communication/Rules/NotificationTemplateRules.php`
- `app/Domains/Communication/Services/NotificationService.php`
- `app/Domains/Role/Events/RoleAssignedToUser.php`
- `app/Domains/Role/Events/RoleRevokedFromUser.php`
- `app/Domains/Role/Policies/RolePolicy.php`
- `app/Domains/Role/Services/RoleService.php`
- `app/Domains/School/Enums/SchoolStatus.php`
- `app/Domains/School/Models/School.php`
- `app/Domains/School/Models/SchoolAdminSetupToken.php`
- `app/Domains/School/Presentation/Http/SchoolAdminSetupController.php`
- `app/Domains/School/Services/InitialSchoolAdminProvisioningService.php`
- `app/Domains/School/Services/SchoolAdminPermissionGrantService.php`
- `app/Domains/School/Services/SchoolAdminProvisioningService.php`
- `app/Domains/System/Enums/AuditAction.php`
- `app/Domains/System/Listeners/AuditLogger.php`
- `app/Domains/System/Services/AuditService.php`
- `app/Domains/User/Enums/UserStatus.php`
- `app/Domains/User/Policies/UserPolicy.php`
- `app/Domains/User/Presentation/Http/AccountInvitationActivationController.php`
- `app/Domains/User/Rules/UserRules.php`
- `app/Domains/User/Services/AccountInvitationActivationService.php`
- `app/Domains/User/Services/AccountProvisioningService.php`
- `app/Domains/User/Services/UserService.php`
- `app/Domains/User/UserServiceProvider.php`
- `app/Domains/Communication/Enums/NotificationChannel.php`
- `app/Http/Controllers/Api/V1/OAuthController.php`
- `app/Http/Controllers/Auth/SchoolGatewayAuthController.php`
- `app/Http/Middleware/EnsureCentralDomain.php`
- `app/Http/Middleware/InitializeTenancyForPanel.php`
- `app/Http/Middleware/InitializeTenancyForSchoolSetup.php`
- `app/Http/Middleware/ValidateApiToken.php`
- `app/Jobs/PruneExpiredTokens.php`
- `app/Jobs/SendNotification.php`
- `app/Listeners/LogApiRequest.php`
- `app/Listeners/LogPermissionRevokedFromRole.php`
- `app/Models/ApiToken.php`
- `app/Models/AuditLog.php`
- `app/Models/ApiAuditLog.php`
- `app/Models/Role.php`
- `app/Models/User.php`
- `app/Models/UserInvitation.php`
- `app/Policies/UserPolicy.php`
- `app/Providers/AppServiceProvider.php`
- `app/Providers/EventServiceProvider.php`
- `app/Services/PanelAccessRegistry.php`
- `app/Services/SchoolService.php`

### Filament presentation layer

- `app/Filament/Resources/CentralUserResource.php`
- `app/Filament/Resources/CentralUserResource/Pages/EditCentralUser.php`
- `app/Filament/Resources/RoleResource/RelationManagers/RolesRelationManager.php`
- `app/Filament/Resources/SuperAdminSchoolResource.php`
- `app/Filament/Resources/SuperAdminSchoolResource/Pages/CreateSuperAdminSchool.php`
- `app/Filament/Resources/SuperAdminSchoolResource/Pages/EditSuperAdminSchool.php`
- `app/Filament/Resources/UserResource.php`
- `app/Filament/Resources/UserResource/Pages/CreateUser.php`
- `app/Filament/Resources/UserResource/Pages/EditUser.php`
- `app/Filament/Resources/UserResource/Pages/ListUsers.php`
- `app/Filament/Resources/UserResource/RelationManagers/RolesRelationManager.php`
- `app/Providers/Filament/AccountantPanelProvider.php`
- `app/Providers/Filament/DirectorPanelProvider.php`
- `app/Providers/Filament/ParentPanelProvider.php`
- `app/Providers/Filament/SchoolAdminPanelProvider.php`
- `app/Providers/Filament/SecretaryPanelProvider.php`
- `app/Providers/Filament/StudentPanelProvider.php`
- `app/Providers/Filament/SuperAdminPanelProvider.php`
- `app/Providers/Filament/TeacherPanelProvider.php`

### Routes, bootstrap, configuration, environment templates, metadata

- `bootstrap/app.php`
- `composer.json`
- `composer.lock`
- `config/app.php`
- `config/auth.php`
- `config/mail.php`
- `config/permission.php`
- `config/sanctum.php`
- `config/services.php`
- `config/session.php`
- `phpstan-baseline.neon`
- `routes/api.php`
- `routes/tenant.php`
- `routes/verification.php`
- `routes/web.php`
- `.env.example` *(only non-secret `APP_ENV`/locale/mail keys observed)*
- `.env.production.example`
- `.env` *(only `APP_ENV` and `MAIL_MAILER` observed; no secrets disclosed)*

### Migrations and seeders

- `database/migrations/0001_01_01_000000_create_users_table.php`
- `database/migrations/2026_07_16_000050_create_personal_access_tokens_table.php` *(mapped but unused in app code)*
- `database/migrations/2026_07_16_000052_create_api_tokens_table.php`
- `database/migrations/2026_07_17_000070_create_audit_logs_table.php`
- `database/migrations/2026_11_06_000001_create_school_admin_setup_tokens_table.php`
- `database/migrations/2026_11_24_020539_create_user_invitations_table.php`
- `database/seeders/NotificationTemplateSeeder.php`
- `database/seeders/PermissionSeeder.php`
- `database/seeders/RoleSeeder.php`
- Hard-delete cascade migrations for invitations, setup tokens, messages, announcements, notification deliveries, and timetable entries were enumerated during discovery for preservation-risk context.

### Views and language files

- `resources/views/account-activation/activate.blade.php`
- `resources/views/account-activation/activated.blade.php`
- `resources/views/account-activation/invalid.blade.php`
- `resources/views/school-admin/setup.blade.php`
- `resources/views/school-admin/setup-done.blade.php`
- `resources/views/auth/school-gateway-login.blade.php`
- `lang/fr.json`
- `lang/fr/auth.php`
- `lang/fr/passwords.php`

### Tests inspected as source evidence only

- `tests/Feature/Api/OAuthApiTest.php`
- `tests/Feature/Auth/SchoolGatewayAuthenticationTest.php`
- `tests/Feature/Domains/Student/StudentAuditTest.php`
- `tests/Feature/Domains/Staff/StaffAuditTest.php`
- `tests/Feature/Domains/Guardian/GuardianAuditTest.php`
- `tests/Feature/Domains/Enrollment/EnrollmentAuditTest.php`
- `tests/Feature/Domains/Timetable/TimetableAuditTest.php`
- `tests/Feature/Domains/User/AccountInvitationActivationHttpTest.php`
- `tests/Feature/Domains/User/AccountInvitationActivationServiceTest.php`
- `tests/Feature/Filament/SchoolAdminStaffProvisioningTest.php`
- `tests/Feature/Filament/UserResourceTest.php`
- `tests/Feature/Routing/SchoolAdminProvisioningTest.php`
- `tests/Feature/Routing/SecurityBaselineTest.php`
- `tests/Feature/Routing/SuperAdminInitialSchoolAdminProvisioningTest.php`
- `tests/Feature/Routing/SuperAdminLoginTest.php`
- `tests/Unit/Domains/System/AuditServiceTest.php`
- `tests/Unit/Domains/User/AccountInvitationActivationServiceTest.php`
- `tests/Unit/Domains/User/AccountProvisioningServiceTest.php`
- `tests/Unit/Jobs/PruneExpiredTokensTest.php`

### Vendor framework source inspected

- `vendor/filament/actions/src/AssociateAction.php`
- `vendor/filament/filament/routes/web.php`
- `vendor/filament/filament/src/Auth/Http/Controllers/LogoutController.php`
- `vendor/filament/filament/src/Auth/Pages/EditProfile.php`
- `vendor/filament/filament/src/Auth/Pages/Login.php`
- `vendor/filament/filament/src/Http/Middleware/Authenticate.php`
- `vendor/filament/filament/src/Http/Middleware/AuthenticateSession.php`
- `vendor/filament/filament/src/Panel/Concerns/HasAuth.php`
- `vendor/filament/filament/src/Resources/RelationManagers/RelationManager.php`
- `vendor/filament/forms/src/Components/Select.php`
- `vendor/filament/support/src/Concerns/Macroable.php`
- `vendor/laravel/framework/src/Illuminate/Auth/Passwords/DatabaseTokenRepository.php`
- `vendor/laravel/framework/src/Illuminate/Auth/Passwords/PasswordBroker.php`
- `vendor/laravel/framework/src/Illuminate/Auth/SessionGuard.php`
- `vendor/laravel/framework/src/Illuminate/Foundation/Auth/User.php`
- `vendor/laravel/framework/src/Illuminate/Foundation/Configuration/Middleware.php`
- `vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php`
- `vendor/laravel/framework/src/Illuminate/Database/Eloquent/Concerns/HasAttributes.php`
- `vendor/laravel/framework/src/Illuminate/Session/Middleware/AuthenticateSession.php`
- `vendor/laravel/framework/src/Illuminate/Validation/Rules/Password.php`

### Documentation inspected

- `Account_Provisioning_Foundation_Specification.md`
- `docs/03_Database/Audit.md`
- `docs/04_Platform/05_Engines/Localization_Engine.md`
- `docs/Decisions/DEC-014-Audit-Everything.md`
- `docs/docs/00_Project_Management/ADR-025_School_Gateway_Implementation_Report.md`
- `docs/docs/00_Project_Management/TASK-09_Student_Guardian_Account_Provisioning_Discovery_Report.md`

### External framework references consulted

- Filament upstream `AssociateAction.php` source at the commit referenced during discovery.
- Filament upstream relationship-management documentation at the commit referenced during discovery.
- Filament upstream patches `a79154e0aa0a71114de260f1161be301a0ca6d79` and `6555a9eb1f0f2c78660056263307bb4e0dad2271`.

## 15. Final Git Working Tree

Command executed after creating this report:

```text
git status --porcelain=v1 -uall
```

Exact output:

```text
?? docs/docs/00_Project_Management/TASK-09_Student_Guardian_Account_Provisioning_Discovery_Report.md
?? docs/docs/00_Project_Management/TASK-10A_Account_Recovery_Administrative_Access_Discovery_Report.md
```

Difference from the initial status:

- Pre-existing `docs/docs/00_Project_Management/TASK-09_Student_Guardian_Account_Provisioning_Discovery_Report.md` remains untracked and unchanged by this task.
- New untracked file created as the only permitted deliverable: `docs/docs/00_Project_Management/TASK-10A_Account_Recovery_Administrative_Access_Discovery_Report.md`.
- No application code, migration, test, configuration, or existing documentation was created or modified.
- No files were staged, committed, pushed, reset, checked out, cleaned, restored, or stashed.
