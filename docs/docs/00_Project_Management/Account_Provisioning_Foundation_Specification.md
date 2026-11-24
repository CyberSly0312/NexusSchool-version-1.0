# Account Provisioning Foundation — V1 Architecture and Implementation Specification

| Field | Value |
|---|---|
| Status | **DRAFT SPECIFICATION — DOCUMENTATION ONLY — NOT APPROVED FOR IMPLEMENTATION** |
| Date | 2026-11-24 |
| Related context | `docs/ADR/ADR-025.md` (PROPOSED), `docs/docs/00_Project_Management/ADR-025_School_Gateway_Implementation_Report.md` |
| Scope | School-scoped account provisioning foundation: institutional, staff, parent/guardian, and student account workflows |
| Application code modified by this task | **None** |
| Migrations/tests modified by this task | **None** |

---

## 1. Purpose and Baseline

This document defines the V1 architecture and implementation specification for NexusSchool centralized account provisioning. It is grounded in read-only discovery performed on 2026-11-24 against `main` @ `fab58b6dd93ebc7d0ebaeabfb8d1f81ac0a800d6`.

### 1.1 Verified current behavior (discovery baseline)

| Area | Verified behavior | Evidence |
|---|---|---|
| User identity | `User` has `school_id`, `first_name`, `last_name`, globally unique `email`, `phone`, `avatar`, `password` (hashed cast), `status` enum, `last_login_at`, `email_verified_at`, remember token, UUID, soft deletes. No invitation/activation/first-login password-change fields. | `app/Models/User.php`, `database/migrations/0001_01_01_000000_create_users_table.php`, `app/Domains/User/Enums/UserStatus.php` |
| User status | `UserStatus`: `Active`, `Inactive`, `Pending`, `Suspended`. | `app/Domains/User/Enums/UserStatus.php` |
| Auth/panel access | `User::canAccessPanel()` delegates to `PanelAccessRegistry`; active user required; non-super-admin requires mapped role and active school; super-admin constrained to `super-admin`. | `app/Models/User.php`, `app/Services/PanelAccessRegistry.php` |
| Roles/permissions | Spatie roles/permissions, `teams=false`, no school/team columns in permission tables. System roles include `super_admin, school_admin, director, vice_director, secretary, accountant, teacher, librarian, registrar, student, parent`. | `config/permission.php`, `database/migrations/2026_07_14_000002_create_permission_tables.php`, `app/Domains/Role/Services/RoleService.php` |
| Tenant scoping | `HasSchoolId` auto-fills `school_id` on create when tenant initialized; provides `scopeForSchool`; **no global query scope**. Filament resources use `ScopedByTenant`/manual scoping. | `app/Traits/HasSchoolId.php`, `app/Filament/Resources/UserResource.php` |
| User management UI | `CentralUserResource` creates/edits central users; `UserResource` creates/edits school users and forces `school_id` in school-admin panel. Both collect password directly; no invitation/activation/first-login flow. | `app/Filament/Resources/CentralUserResource.php`, `app/Filament/Resources/UserResource.php`, related pages |
| Student domain | Student has optional `user_id`, school-scoped identifiers, status, soft deletes. Filament CreateStudent creates student via `StudentService::register()` with embedded guardians; **no student/guardian User provisioning**. | `app/Models/Student.php`, `app/Domains/Student/Infrastructure/Models/Student.php`, `app/Domains/Student/Presentation/Filament/Resources/StudentResource.php`, `CreateStudent.php`, `StudentService.php` |
| Guardian domain | Guardian has optional `user_id`, school-scoped unique `guardian_number`, unique `phone_number` per school, unique `email` per school when present. | `app/Models/Guardian.php`, `app/Domains/Guardian/Infrastructure/Models/Guardian.php`, `database/migrations/2026_07_15_000015_create_guardians_table.php` |
| Student-guardian relationship | `student_guardians` is a first-class relationship table/model (`StudentGuardianRelationship`) with role flags: primary, emergency contact, authorized pickup, financial responsible, notification recipient, medical contact, pickup window, relationship dates/history. Unique `(school_id, guardian_id, student_id, deleted_at)`. | `database/migrations/2026_08_01_000013...`, `2026_08_03_000002...`, `app/Domains/Guardian/Infrastructure/Models/StudentGuardianRelationship.php` |
| Enrollment | Enrollment is a separate domain aggregate; CreateEnrollment uses `EnrollmentService::enroll()`; no account provisioning. | `app/Domains/Enrollment/Infrastructure/Models/Enrollment.php`, `CreateEnrollment.php` |
| Notifications | Notification/channel/template/delivery domain models exist; `SendNotification` job contains stub comment for provider integration. Email infra is standard Laravel; SMS provider not verified/configured. | `app/Jobs/SendNotification.php`, `app/Models/Notification*.php`, `app/Domains/Communication/*` |
| Password reset | `password_reset_tokens` table exists (Laravel default). No custom account invitation/activation flow found. | `database/migrations/0001_01_01_000000_create_users_table.php` |

### 1.2 Proposed V1 decisions (target state)

1. Every non-platform account belongs to one school through `users.school_id`.
2. Cross-school parent identity is explicitly out of scope for V1.
3. A parent can have multiple children within the same school through the existing guardian/student relationship model.
4. Existing `User`, `Guardian`, `Student`, Spatie roles, and `PanelAccessRegistry` remain architectural foundations.
5. Account creation must be centralized in application/domain services, not duplicated across Filament pages.
6. Tenant ownership must always be derived from trusted tenant context, never from an untrusted request payload.
7. Account activation must use secure, expiring, single-use tokens.
8. Newly provisioned accounts must complete initial password setup before accessing protected panels.
9. Email is the initial notification channel; SMS integration is deferred behind a notification abstraction.
10. Existing accounts must not be unexpectedly locked out by the new first-login mechanism.

### 1.3 Unresolved decisions (must be clarified before implementation)

- **CentralUserResource schema mismatch:** `CentralUserResource` uses `name` and manual bcrypt dehydration while `User` uses `first_name/last_name` and hashed cast. Confirm canonical behavior before reuse.
- **Provisioning role matrix detail:** exact roles School Admin may provision (especially `director`, `vice_director`) and whether any approval workflow is required.
- **Student account policy:** when student accounts are created, age/contact constraints, and whether parent-mediated activation is required.
- **Duplicate-match UX:** manual review queue vs inline conflict resolution in Filament.
- **Pending/invited status semantics:** whether to reuse `Pending`, add a new status, or represent invitation state only in invitation table.
- **Revocation scope:** whether role/school/status change invalidates sessions, remember tokens, API tokens, and Spatie permission cache in same slice or separate security slice.
- **Cross-school identity future model:** linked identities vs school memberships; requires separate ADR/governance decision.

### 1.4 Future enhancements (explicitly out of V1)

- Cross-school parent identity / tenant-switching identity.
- SMS/WhatsApp provider adapters and delivery.
- Self-service registration (public) for any role.
- Bulk import/provisioning with external HR/SIS sync.
- MFA enrollment during activation.
- SCIM/SSO/directory integration.

---

## 2. Existing Architectural Constraints

The implementation must explicitly account for these constraints. Do not redesign them unnecessarily in V1.

| Constraint | Impact | V1 handling |
|---|---|---|
| Global unique `users.email` | Same email cannot exist in two User rows; blocks simple cross-school same-email accounts. | Preserve constraint. Detect conflicts; require explicit administrative resolution. No silent merge. |
| Singular `users.school_id` | User belongs to at most one school in current model. | All non-platform provisioned accounts must have a valid school_id; super-admin remains platform case. |
| Spatie `permission.teams=false` | Roles are global, not school-scoped. | Assign only allowed global role names; authorization remains server-side via role + school + panel registry. |
| `HasSchoolId` has no global scope | Queries can leak across tenants if not explicitly scoped. | All provisioning queries/mutations must scope by trusted tenant/school context and use explicit `where('school_id', ...)` / domain scoping. |
| `UserStatus` enum exists | `Pending` exists; semantics currently generic. | Reuse carefully; do not overload without clear definition. Prefer invitation table as source of activation truth. |
| App-level and domain-level Student/Guardian models coexist | Duplicate representations can cause inconsistent writes. | New provisioning code should use canonical domain models/services for the affected workflow and document chosen target. |
| Nullable `students.user_id`, `guardians.user_id` | Academic/guardian records may exist without User accounts. | Provisioning must link accounts idempotently and tolerate existing records without users. |
| `student_guardians` relationship semantics | First-class relationship with role flags/history. | Preserve flags/history; do not replace with simple pivot-only logic. |
| School Gateway + PanelAccessRegistry | Centralized auth entry and panel authorization already exist. | First-login enforcement must integrate with gateway and all panels; no parallel auth system. |

---

## 3. Account Provisioning Workflows

## 3.A Institutional Account Provisioning

**Actor:** Super Admin (central context).

**Workflow:**
1. Super Admin selects an existing active school from trusted server-side data (not free-form tenant payload).
2. System validates school exists, is active/eligible, and actor has required permission.
3. System collects School Admin identity: first name, last name, email, optional phone.
4. System checks duplicate/conflict rules (Section 5).
5. `AccountProvisioningService` creates or reuses account only by explicit safe rule; for V1 default, create new User when no conflict and no existing suitable account.
6. System binds `school_id` to selected school, sets initial status per policy (recommended `Pending` until activation, or `Inactive` + invitation state; decision required), assigns `school_admin` role only.
7. System creates invitation (purpose `school_admin_initial_activation`) with hashed expiring single-use token bound to user+school.
8. System dispatches activation email through notification abstraction.
9. Audit events recorded for provisioning, invitation generation, delivery attempt/outcome.
10. User follows activation link, sets initial password, invitation consumed, session regenerated, user becomes eligible for panel access per existing `PanelAccessRegistry` and account-state rules.

**Security invariants:**
- Selected school validated server-side; no trust in request `school_id` for authorization.
- Only `school_admin` role assignable in this flow unless separately authorized by explicit future decision.
- No plaintext password generated/emailed.

## 3.B Staff Account Provisioning

**Actor:** School Admin (tenant context).

**Supported roles to evaluate (V1 candidate set):**

| Role | V1 recommendation | Rationale / note |
|---|---|---|
| `director` | Allow with explicit permission and audit | High-privilege school role; confirm governance. |
| `vice_director` | Allow with explicit permission and audit | Mapped in roles; panel mapping currently absent in `PanelAccessRegistry` - authorization impact must be resolved before provisioning. |
| `secretary` | Allow | Existing panel/role mapping. |
| `accountant` | Allow | Existing panel/role mapping. |
| `teacher` | Allow | Existing panel/role mapping. |
| `librarian` | Defer unless panel/access path defined | Role exists in system roles; no panel mapping in `PanelAccessRegistry`. |
| `registrar` | Defer unless panel/access path defined | Role exists in system roles; no panel mapping in `PanelAccessRegistry`. |
| `school_admin` | Deny by default | Privilege escalation / governance risk; separate authorized flow only. |
| `super_admin` | Deny always in tenant flow | Platform role; central-only. |

**Workflow:**
1. School Admin initiates staff provisioning in current tenant context.
2. System derives school from trusted tenant context (`tenant()` / authenticated school context), not request payload.
3. System validates requested role is in School Admin allowlist and actor has provisioning permission.
4. System performs duplicate/conflict checks within school.
5. Provisioning service creates User with `school_id=current school`, assigns exactly one authorized staff role (or explicitly approved role set), creates invitation, sends activation email.
6. User completes initial password setup before protected panel access.

**Security invariants:**
- Tenant ownership derived from trusted context only.
- Role allowlist enforced server-side; reject `super_admin`, `school_admin`, and unmapped/unapproved roles.
- No privilege escalation by manipulating form/request role IDs/names.
- All actions audited.

## 3.C Parent Account Provisioning

**Context:** during student registration/enrolment, or dedicated guardian management workflow.

**Workflow:**
1. Capture guardian contact information (name, relationship, email, phone, preferred contact, role flags as applicable).
2. Search for existing Guardian in **current school** using deterministic rules (Section 5).
3. If exact reliable match found: reuse Guardian record; do not create duplicate.
4. If Guardian has linked active/invitable User: link student to Guardian; reuse account; do not email credentials; optionally send informational notification per policy.
5. If Guardian exists without linked User and identity/contact is sufficient: create invitation/provision user through service, link `guardians.user_id` after successful account creation.
6. If no suitable Guardian exists: create Guardian and, if policy requires/permits, provision linked parent User.
7. If ambiguous match: stop automatic linking; create administrative review task/conflict record; require explicit human resolution.
8. Link student to guardian through existing `student_guardians`/`StudentGuardianRelationship` semantics, preserving role flags and history.

**Security/identity invariants:**
- **No automatic association based only on unverified phone number.** Phone-only may create "possible match requiring review" unless corroborated by stronger identifiers per approved rule.
- Reuse existing parent account only when identity matching is sufficiently reliable and school-scoped.
- A parent/guardian may be linked to multiple children in the same school.
- Preserve `student_guardians` relationship flags/history; no destructive overwrite.

## 3.D Student Account Provisioning

**Separate operations (must not be conflated):**
1. **Student academic record creation** — creates/updates `Student` and academic/enrolment data.
2. **Student account creation** — creates `User` with `student` role and links `students.user_id`.
3. **Student account activation** — delivers invitation and completes initial password setup.

**V1 policy recommendation:**
- Student academic record creation does **not** automatically create a login account.
- Student account creation is optional, disabled by default unless school policy explicitly enables it.
- If enabled, account creation requires sufficient identity/contact data and an approved delivery channel. For minors, activation should be parent-mediated or school-admin mediated; no assumption that student has independent email.

**Credential handling:**
- Never expose permanent passwords to parents or staff.
- Use activation/invitation link or one-time password setup token delivered through approved channel.
- If no secure delivery channel is available, account remains provisioned-but-not-activated; staff receives administrative status, not credentials.

---

## 4. Invitation and Activation Architecture

## 4.1 Recommended model

**Proposed:** dedicated `user_invitations` table (preferred over storing all invitation state on `users`).

Rationale: supports multiple invitations/history, revocation, auditability, expiry, token rotation, and avoids overloading `users` with transient security state.

## 4.2 Proposed invitation fields

| Field | Type | Purpose |
|---|---|---|
| `id` | ULID/UUID/Bigint (project convention) | Primary identifier |
| `user_id` | FK users | Intended account |
| `school_id` | FK tenants/schools, nullable for platform cases only if needed | Tenant binding; required for school-scoped invitations |
| `invited_by_user_id` | FK users nullable | Actor audit |
| `purpose` | string/enum | e.g. `school_admin_initial`, `staff_initial`, `guardian_initial`, `student_initial`, `password_reset_replacement` (future careful) |
| `email` | string | Delivery target snapshot |
| `token_hash` | string, indexed/unique as needed | Hashed token only; never plaintext |
| `expires_at` | datetime | Expiry |
| `consumed_at` | datetime nullable | Single-use consumption |
| `revoked_at` | datetime nullable | Revocation |
| `revocation_reason` | string nullable | Audit/ops |
| `delivery_status` | string/enum nullable | pending/sent/failed/bounced/unknown |
| `delivered_at` | datetime nullable | Last successful delivery |
| `last_delivery_error` | text nullable | Sanitized failure detail |
| `metadata` | json nullable | Minimal non-sensitive context (role, flow) |
| timestamps/soft delete? | per convention | Audit/lifecycle |

## 4.3 Token rules

- Cryptographically secure random token (high entropy).
- Store only hash (e.g. HMAC/hash strategy consistent with Laravel security practices); never persist plaintext token.
- Expiring (short TTL; exact duration unresolved, recommend 24-72h configurable).
- Single-use: successful activation sets `consumed_at` and invalidates token.
- Bound to intended user and school/purpose; validation must check all bindings.
- Invalidated on revocation, consumption, expiry, or superseding invitation policy (e.g. latest-only).
- Activation routes must be rate-limited and generic in failure responses.

## 4.4 Initial password setup flow

1. User opens activation link containing opaque token (and non-sensitive identifier if needed).
2. System validates token hash, expiry, not consumed/revoked, user/school binding, account eligibility.
3. User sets new password (and confirmation) under password policy.
4. Transaction: update password, mark invitation consumed, set account activation state, clear/reset relevant tokens as needed, record audit events.
5. Regenerate session/CSRF token; authenticate only through explicit, secure flow defined by implementation (avoid auto-login unless separately approved).
6. Redirect to gateway/panel-appropriate next step.

**Account state effect (proposed):** successful initial password setup transitions provisioned account to active/usable state for panel access, subject to `UserStatus`, school active state, role mapping, and `PanelAccessRegistry`.

---

## 5. First-Login Enforcement

## 5.1 Proposed mechanism

Use a **centralized enforcement middleware** (or equivalent Filament/auth middleware stack) applied consistently to protected panel/gateway routes, with explicit allowlist for activation/password-setup/logout/public routes.

**Recommended state signal:** invitation/activation state in dedicated table and/or explicit user field such as `password_set_at`/`must_complete_initial_setup_at`. Do not rely only on `UserStatus::Pending` because `Pending` may already have other semantics.

## 5.2 Requirements

- Newly provisioned accounts cannot access protected panel pages before completing initial password setup.
- Enforcement applies to all relevant Filament panels and School Gateway protected paths.
- Activation/password setup routes remain accessible without normal authenticated panel access, but token validation is mandatory.
- Session regenerated after password setup/activation.
- Invitation token invalidated on success; revoked/expired tokens rejected.
- Existing accounts predate feature: **no lockout**. Migration/backfill must treat existing active users as already setup-complete (e.g. set `password_set_at`/setup-complete marker during migration for existing users with usable passwords, or gate only accounts with active provisioning invitation state).
- No per-panel custom password-change logic; one shared service/controller/view flow.

## 5.3 Compatibility considerations

- **Filament panels:** apply via panel middleware or shared route middleware; test each panel.
- **School Gateway:** gateway login may authenticate credentials, but post-auth routing must redirect to mandatory setup if account not activated.
- **Password reset vs invitation:** keep flows distinct; password reset is for existing activated accounts, invitation/activation for newly provisioned initial setup.

---

## 6. Duplicate Identity Rules

All matching is **school-scoped** unless explicitly stated.

| Case | Rule | V1 action |
|---|---|---|
| Exact reliable match | Same school + verified existing link (e.g., existing Guardian already linked to same student; or exact email match on User in same school with compatible identity) | Reuse/link; audit; no duplicate account creation. |
| Existing User by email in same school | Email matches existing User in same school | Do not create new User. Evaluate role/link compatibility; if safe, link/reuse per workflow; otherwise conflict. |
| Existing User by email in different school | Email matches global unique `users.email` | **Conflict/block in V1**; cannot create second User. Record as unresolved cross-school identity case; require admin decision. |
| Existing Guardian by email/phone in same school | Guardian email/phone matches | Treat as possible/exact depending on corroborating data; never auto-link on unverified phone alone. |
| Possible match requiring review | Phone-only match, name similarity, partial contact match, or conflicting identity attributes | Create review/conflict item; no silent merge/link. |
| Conflicting records | Same email/phone points to incompatible identities or conflicting school ownership | Block automatic provisioning; require explicit admin resolution. |
| Existing Guardian without linked User | Guardian record exists, `user_id` null | May provision User and link `guardians.user_id` after duplicate checks and explicit workflow. |
| Existing User without linked Guardian | User exists with parent role/context but no Guardian link | May create/link Guardian only with explicit corroboration; no silent merge. |

**Non-negotiable rules:**
- No silent merge or overwrite of existing identities.
- No automatic association based only on unverified phone number.
- Preserve global unique `users.email` in V1 unless separately approved migration design.
- All matching decisions audited.

---

## 7. Authorization Matrix

| Actor | Permitted provisioning responsibilities | Explicitly denied / constrained |
|---|---|---|
| Super Admin | Create initial School Admin accounts for selected existing school; manage platform provisioning workflows explicitly authorized. | Must not bypass tenant validation/audit; cross-school identity actions out of V1. |
| School Admin | Create authorized school staff accounts within current tenant; initiate controlled parent/student account provisioning workflows as defined. | Cannot create `super_admin`; cannot create `school_admin` unless separately authorized; cannot assign unapproved roles; cannot provision outside trusted tenant context. |
| Director | No additional provisioning authority by default. | Any provisioning capability requires explicit governance decision, permission, audit, and tests. |
| Secretary | No additional provisioning authority by default. | Same as Director. May assist operationally only through explicitly authorized workflows later. |
| Accountant | No provisioning authority. | N/A |
| Teacher | No provisioning authority. | N/A |
| Parent | No administrative account provisioning. | No public/self-service provisioning; access only through controlled school workflow. |
| Student | No administrative account provisioning. | No public/self-service provisioning. |

**Additional rule:** parent/student account creation occurs only through controlled school workflows (enrolment/guardian management/admin action), not unrestricted public registration.

---

## 8. Notification Architecture

## 8.1 V1

- **Email** is the initial channel for invitations/activation.
- Reuse existing Laravel mail/notification infrastructure where appropriate; wrap behind an application notification abstraction (e.g., `AccountInvitationNotifier` / channel strategy) to avoid hard-coding mail in services.
- Record delivery attempts and outcomes (invitation delivery status/timestamps/error summary; audit event).
- Handle failed delivery without inconsistent account state: account/invitation creation and delivery dispatch should be transactional where possible; if delivery fails after commit, mark invitation delivery failed and expose retry action. Never expose plaintext token in logs.

## 8.2 Future

- SMS provider adapter (deferred).
- WhatsApp adapter if justified.
- In-app notification.

**Constraint:** do not claim SMS is currently configured or operational.

---

## 9. Audit and Revocation Requirements

## 9.1 Auditable events

Minimum auditable events (via existing audit infrastructure where appropriate):

| Event | Minimum data |
|---|---|
| `account.provisioned` | actor, target user, school, role(s), flow/purpose |
| `account.invitation_generated` | actor, user, school, invitation id, purpose, expiry |
| `account.invitation_delivered` | invitation id, channel, recipient (masked), result |
| `account.invitation_delivery_failed` | invitation id, channel, sanitized error |
| `account.invitation_revoked` | actor, invitation id, reason |
| `account.activated` | user, school, invitation id |
| `account.initial_password_set` | user, school |
| `account.role_assigned` / `account.role_changed` | actor, user, old/new roles |
| `guardian.linked_to_student` | actor, guardian, student, relationship id/flags |
| `guardian.student_relationship_removed` | actor, guardian, student, relationship id |

## 9.2 Revocation / lifecycle considerations

Known unresolved risk: session, remember-token, API-token, and Spatie permission-cache revocation on role/school/status changes. The provisioning spec must not worsen this risk.

**V1 requirement:** define hooks/events for role/status/school changes and document revocation expectations. Implementation may be phased, but every slice touching role/status/school changes must state its revocation behavior. At minimum, activation/password setup should regenerate session; role/school deactivation should have a documented follow-up security slice if not implemented immediately.

---

## 10. Implementation Sequence (Independently Testable Slices)

### Slice 1 — Invitation schema and model foundation
- **Files/domains likely involved:** `database/migrations/*create_user_invitations_table*`, `app/Models/UserInvitation.php` or domain equivalent, `app/Domains/User/...`, factories/seeders as needed.
- **Dependencies:** none; requires final decision on status/field naming.
- **Security invariants:** token hash only; expiry/consumed/revoked fields; user+school binding; no plaintext token persistence.
- **Required tests:** migration/schema tests, model casts/relations, factory, token-hash storage assertion.
- **Acceptance criteria:** invitation record can be created, expires, is consumed once, and cannot expose plaintext token.

### Slice 2 — Centralized account provisioning service
- **Files/domains likely involved:** `app/Domains/User/Application/Services/AccountProvisioningService.php` (or agreed path), DTOs/actions, role assignment integration, audit events.
- **Dependencies:** Slice 1.
- **Security invariants:** trusted tenant context; server-side role allowlist; no privilege escalation; duplicate conflict detection hooks; transactional consistency.
- **Required tests:** unit/feature tests for school admin/staff provisioning, invalid role rejection, cross-tenant rejection, duplicate email conflict.
- **Acceptance criteria:** one service can provision supported accounts with correct school/role/invitation/audit outcomes.

### Slice 3 — Invitation generation, validation, and activation
- **Files/domains likely involved:** invitation service/action, activation controller/routes/views, password setup form/request validation.
- **Dependencies:** Slices 1-2.
- **Security invariants:** hash lookup, expiry, single-use, user/school binding, rate limiting, generic errors, session regeneration, token invalidation.
- **Required tests:** valid activation, expired token, consumed token, revoked token, wrong school/user binding, weak password rejection.
- **Acceptance criteria:** invited user can set initial password once; token cannot be reused.

### Slice 4 — First-login enforcement across existing panels and gateway
- **Files/domains likely involved:** middleware, panel providers, gateway controller/routes, shared password-setup UI.
- **Dependencies:** Slice 3.
- **Security invariants:** protected routes blocked until setup complete; activation routes allowed; existing active users unaffected; session regeneration.
- **Required tests:** newly provisioned user blocked on all mapped panels/gateway; existing user unaffected; activated user gains normal access.
- **Acceptance criteria:** no protected panel access before initial setup for newly provisioned accounts.

### Slice 5 — Super Admin School Admin provisioning interface
- **Files/domains likely involved:** Filament central/super-admin resource/page/action, provisioning service integration.
- **Dependencies:** Slices 2-4.
- **Security invariants:** active school selection server-side; only `school_admin` role; audit.
- **Required tests:** Super Admin can provision school admin; invalid school rejected; role escalation rejected.
- **Acceptance criteria:** Super Admin can create initial school admin with invitation email flow.

### Slice 6 — School Admin staff provisioning interface
- **Files/domains likely involved:** `UserResource`/dedicated staff provisioning action in school-admin panel.
- **Dependencies:** Slices 2-4.
- **Security invariants:** trusted tenant context; role allowlist; no school_id override; no super_admin/school_admin creation.
- **Required tests:** allowed staff roles succeed; disallowed roles rejected; tenant scoping enforced.
- **Acceptance criteria:** School Admin can provision authorized staff accounts only.

### Slice 7 — Guardian duplicate detection and account linking
- **Files/domains likely involved:** Guardian domain service/action, duplicate detection rules, conflict review model/UI (minimal).
- **Dependencies:** Slices 2-4; Guardian domain models.
- **Security invariants:** school-scoped matching; no auto-link on unverified phone only; no silent merge; preserve relationship flags.
- **Required tests:** exact match reuse, possible match review, conflicting email, guardian without user, existing user without guardian.
- **Acceptance criteria:** guardian linking is idempotent, auditable, and safe against false merges.

### Slice 8 — Student enrolment integration
- **Files/domains likely involved:** Student/Enrollment Filament flows, orchestration action/service, guardian linking.
- **Dependencies:** Slices 6-7.
- **Security invariants:** trusted tenant context; no credential exposure; student account optional by policy.
- **Required tests:** enrolment with existing parent reuse, new parent provisioning, multiple children same guardian, student account optional path.
- **Acceptance criteria:** enrolment can link/provision family accounts without breaking existing student/enrolment behavior.

### Slice 9 — Email delivery and failure handling
- **Files/domains likely involved:** notification/mail classes, delivery status updates, retry action/job.
- **Dependencies:** Slices 2-3.
- **Security invariants:** no plaintext token logging; sanitized errors; retry does not mint uncontrolled tokens unless policy allows rotation.
- **Required tests:** mail sent, delivery failure recorded, retry behavior, invitation still valid/rotated per policy.
- **Acceptance criteria:** delivery outcomes visible/auditable; failures do not corrupt account state.

### Slice 10 — Student account policy implementation
- **Files/domains likely involved:** policy/config, student provisioning UI/action, guardian-mediated activation flow.
- **Dependencies:** Slices 3,4,8.
- **Security invariants:** optional by policy; no credential exposure; age/contact rules enforced.
- **Required tests:** disabled policy blocks auto-provisioning; enabled policy provisions correctly; activation secure.
- **Acceptance criteria:** student accounts can be provisioned only under explicit policy with secure activation.

---

## 11. Risks and Mitigations

| Risk | Impact | Mitigation |
|---|---|---|
| Global email uniqueness blocks cross-school identity | Cannot create same-email account in another school | Explicit conflict handling; defer cross-school identity to separate ADR. |
| First-login enforcement locks out existing users | Production outage/support burden | Backfill/setup-complete marker for existing users; feature flag/rollout tests. |
| Auto-linking wrong guardian | Privacy/data breach within school | No phone-only auto-link; manual review for ambiguous matches; audit. |
| Privilege escalation through role assignment | Unauthorized admin access | Server-side allowlists, permission checks, tenant binding, tests. |
| Token leakage | Account takeover | Hash-only storage, short expiry, single-use, rate limit, no logs. |
| Delivery failure | User cannot activate | Delivery status tracking, retry, admin visibility. |
| Model duplication (app/domain Student/Guardian) | Inconsistent writes | Choose canonical model/service per flow; document and test. |
| Missing revocation on role/status changes | Stale access | Documented hook; separate security slice if needed. |

---

## 12. Recommended First Implementation Slice

**Recommend Slice 1: Invitation schema and model foundation** (after resolving status/field naming decisions), immediately followed by Slice 2 in planning but not in same change-set.

Why first: it establishes the security-critical activation primitive (hashed, expiring, single-use, user/school-bound invitations) required by all provisioning flows, without changing user-facing behavior. It is independently testable, low blast radius, and forces explicit decisions on token storage, expiry, and account-state semantics before UI/service rollout.

**Pre-implementation clarifications required before Slice 1 coding:**
1. Reuse `UserStatus::Pending` vs add explicit setup-complete marker/field.
2. Invitation ID/type conventions and exact table name.
3. Token TTL and resend/rotation policy.
4. Whether platform/super-admin invitations need `school_id` nullable or separate flow.

---

## 13. Documentation Safety Confirmation

- Documentation only; no application code, migrations, or tests changed by this specification task.
- `docs/ADR/ADR-025.md` not modified.
- `docs/docs/00_Project_Management/ADR-025_School_Gateway_Implementation_Report.md` not modified by this specification task.
- Existing working-tree changes preserved.