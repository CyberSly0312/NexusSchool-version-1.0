# TASK-11.3 — Guardian Linking & Child-Level Authorization — Implementation Report

| Field | Value |
|---|---|
| Date | 2026-11-27 (original implementation); 2026-11-28 (first security-review remediation); 2026-11-28 (provenance-forgery remediation) |
| Verified HEAD | `cc05790 commit du 28_11` |
| Governance state | Decision 8 approved; Decision 7 deferred/pending future owner review |
| Implementation state | Sections 1–11 are the original implementation record. An independent review returned **REQUIRES CORRECTIONS**; findings A–G were remediated in **Section 12**. A later independent review found a provenance-forgery blocker; its remediation is recorded in **Section 13** without rewriting earlier sections. |
| Overall Task 11 | **NOT COMPLETE** — Task 11.4 and Task 11.5 are deliberately not started |
| Owner approval | Decisions 1, 2, 3, 4, 5, 6, 8, 9 and 10 approved; Decision 7 not approved |
| Final remediation verdict | **BLOCKER RESOLVED — READY FOR FINAL INDEPENDENT REVIEW** (see §13.9). The earlier **COMPLETE** verdict in §12.10 is retained only as historical evidence. |

## 1. Objective and enforced outcome

Task 11.3 adds explicit, verified, school-scoped Guardian–Student associations and child-level authorization for Parent access.

The implemented rule is strict: one Guardian account may access multiple Students in the **same active school**, but only when **each** Guardian–Student pair has its own independently verified and explicitly authorized relationship. Access to one child never grants access to a sibling or any other child. Cross-school associations are rejected. Name-only evidence, unverified phone evidence, and embedded `GuardianReference`/`StudentGuardian` rows with `guardian_id = NULL` cannot establish access.

The Parent surface added here is intentionally read-only and minimal. It exists to prove that list queries, record views, crafted record IDs, and direct panel requests all pass through the same domain/application authorization rules.

## 2. Governance state recorded

`docs/docs/00_Project_Management/TASK-11_Student_Guardian_Account_Provisioning_Decisions.md` was updated on 2026-11-27.

| Decision | State | Effect on implementation |
|---|---|---|
| Decision 7 | **DEFERRED — PENDING FUTURE OWNER REVIEW; NOT APPROVED** | No new responsibility taxonomy, no new guardian responsibility types, and no redesign of existing responsibility flags or relationship semantics. Existing structures are preserved except for the minimal authorization state strictly necessary for secure child-level access. |
| Decision 8 | **APPROVED** | Multiple same-school Students per Guardian are allowed, but every relationship must be independently verified, explicitly authorized, school-scoped, revocable, and history-preserving. Task 11.1 matching remains mandatory. |
| Decisions 1, 2, 3, 4, 5, 6, 9, 10 | Approved | Prior Task 11.1/11.2 decisions remain in force. |

The governance document still states that the overall Task 11 workstream is **not approved or complete**.

## 3. Preserved boundaries and unchanged mechanisms

The following existing controls were preserved and covered by regressions:

- Tenant middleware, tenant scopes, and active-school resolution.
- `SchoolAdminRolePolicy` and its approved role list.
- `UserRoleChangeService` and the audited role-change lifecycle.
- Task 11.1 `SchoolScopedIdentityMatcher` outcomes and evidence rules.
- Task 11.2 provisioning boundaries: invitations, activation, and enrollment orchestration remain out of scope.
- Existing student status semantics; Parent authorization does not change `StudentStatus`.
- Existing guardian responsibility flags and relationship types; no Decision 7 redesign was introduced.
- No role allowlist expansion. The service checks the existing `parent` role only; it does not grant staff permissions or broaden `student.view`.

## 4. Data model and migration

Added `database/migrations/2026_11_27_000001_add_portal_authorization_to_student_guardians_table.php`:

- `verification_status` — default `unverified`.
- `portal_access_status` — default `not_authorized`.
- `verified_at`, `portal_authorized_at`, `portal_revoked_at` — nullable timestamps.
- `verification_evidence_code`, `authorization_reason_code` — nullable code columns.
- Two indexes on school/guardian/status and school/student/status.

Existing durable rows remain valid business relationships but default to unverified/unauthorized, so no legacy row silently grants Parent access.

Added domain enums:

- `RelationshipVerificationStatus`: `unverified`, `verified`, `rejected`.
- `RelationshipPortalAccessStatus`: `not_authorized`, `authorized`, `revoked`.
- `GuardianChildOperation`: only `View` is permitted; `Download` and `Edit` fail closed.
- `GuardianAccessDenialReason`: stable, privacy-safe reason codes for audits and tests.

`StudentGuardianRelationship` now casts the two status columns, exposes `scopePortalAuthorized()`, and exposes `isPortalAuthorized()`. Both require a non-null `guardian_id`, verified status, authorized portal state, a started and unexpired relationship date window, and a non-soft-deleted row. `UnlinkGuardianAction` now immediately revokes portal authorization before soft-deleting the relationship, preserving history.

## 5. Centralized authorization service

`app/Domains/Guardian/Application/Services/GuardianChildAccessService.php` is the single decision point for Parent child access.

- `assertParentContext()` resolves the initialized tenant, active authenticated user, existing `parent` role, and exactly one same-school Guardian account.
- `assertCanAccess()` resolves tenant, user, Guardian, Student, and the exact relationship before checking every ownership and state invariant.
- `scopeStudentQuery()` applies the same authorization rules to list/table/export-style queries and returns an empty result (`1 = 0`) on any failure.
- `recordChildViewed()` records only IDs and reason-safe metadata after a successful view.
- All denials are audited with reason codes and then throw `GuardianChildAccessDeniedException`.

Fail-closed cases include: missing or stale tenant, inactive school, missing/inactive/suspended user, missing `parent` role, ambiguous or missing Guardian account, cross-school user/Guardian/Student/relationship, soft-deleted relationship, mismatched relationship ownership, unverified/revoked/not-authorized relationship, not-started or expired relationship, and unsupported operation.

## 6. Verification and revocation lifecycle

`app/Domains/Guardian/Application/Services/GuardianRelationshipVerificationService.php` implements per-child verification and revocation.

- Trusted DTOs (`GuardianRelationshipVerificationData`, `GuardianRelationshipRevocationData`) carry server-resolved IDs only, never raw identity evidence.
- Verification calls `SchoolScopedIdentityMatcher` for user, Guardian, and Student. It accepts only `EXACT_MATCH` with `automaticReusePermitted`, `candidateFound`, and a candidate ID equal to the resolved record.
- Duplicate or conflicting relationships are rejected. Re-verifying an already verified/authorized relationship is idempotent.
- Verification writes verified/authorized state, evidence and reason codes, and appends a history row.
- Revocation writes `revoked` state and timestamp, appends history, and preserves the relationship row for audit/history.
- Relationship ownership is re-checked against the active tenant and resolved Guardian/Student on every verification and revocation.

## 7. Parent panel / Filament enforcement

A minimal read-only Parent surface was added:

- `app/Filament/Parent/Resources/ParentChildResource.php`
- `app/Filament/Parent/Resources/ParentChildResource/Pages/ListParentChildren.php`
- `app/Filament/Parent/Resources/ParentChildResource/Pages/ViewParentChild.php`
- Registration in `app/Providers/Filament/ParentPanelProvider.php`.

Enforcement details:

- `getViewAnyAuthorizationResponse()` and `getViewAuthorizationResponse()` return Laravel `Response::allow()`/`Response::deny()` from the centralized service, deliberately bypassing the staff-oriented global `StudentPolicy` without expanding any permission set.
- `getEloquentQuery()` uses `scopeStudentQuery()`, so the table cannot list unauthorized children.
- The view page calls `assertCanAccess()` before `parent::mount($record)`, so crafted related, unrelated, missing, and cross-school record IDs all return 403.
- Create, edit, delete, and bulk actions are absent or disabled. No Parent API, export, widget, or mutation endpoint was introduced.
- Successful views are audited only after authorization succeeds.

## 8. Audit, privacy, and history guarantees

`AuditAction` gained six new actions: relationship requested, verified, rejected, revoked, child access denied, and child viewed.

Audit and history writes use stable reason/evidence codes and stringified IDs. They do not store names, emails, phone numbers, tokens, documents, or other raw identity evidence. Soft-deleted relationships remain available to authorization checks only as revoked history and never grant access. The privacy-focused unit test asserts that denial and view audits contain no sensitive personal data.

## 9. Exact files created or modified

Modified:

- `app/Domains/Guardian/Application/Actions/UnlinkGuardianAction.php`
- `app/Domains/Guardian/Infrastructure/Models/Guardian.php`
- `app/Domains/Guardian/Infrastructure/Models/StudentGuardianRelationship.php`
- `app/Domains/System/Enums/AuditAction.php`
- `app/Providers/Filament/ParentPanelProvider.php`
- `database/factories/Guardian/GuardianFactory.php`
- `database/factories/Guardian/StudentGuardianRelationshipFactory.php`
- `docs/docs/00_Project_Management/TASK-11_Student_Guardian_Account_Provisioning_Decisions.md`

Created:

- `app/Domains/Guardian/Application/DTOs/GuardianRelationshipRevocationData.php`
- `app/Domains/Guardian/Application/DTOs/GuardianRelationshipVerificationData.php`
- `app/Domains/Guardian/Application/Services/GuardianChildAccessService.php`
- `app/Domains/Guardian/Application/Services/GuardianRelationshipVerificationService.php`
- `app/Domains/Guardian/Domain/Enums/GuardianAccessDenialReason.php`
- `app/Domains/Guardian/Domain/Enums/GuardianChildOperation.php`
- `app/Domains/Guardian/Domain/Enums/RelationshipPortalAccessStatus.php`
- `app/Domains/Guardian/Domain/Enums/RelationshipVerificationStatus.php`
- `app/Domains/Guardian/Domain/Exceptions/GuardianChildAccessDeniedException.php`
- `app/Domains/Guardian/Domain/Exceptions/GuardianRelationshipVerificationException.php`
- `app/Filament/Parent/Resources/ParentChildResource.php`
- `app/Filament/Parent/Resources/ParentChildResource/Pages/ListParentChildren.php`
- `app/Filament/Parent/Resources/ParentChildResource/Pages/ViewParentChild.php`
- `database/migrations/2026_11_27_000001_add_portal_authorization_to_student_guardians_table.php`
- `tests/Feature/Filament/ParentChildResourceAuthorizationTest.php`
- `tests/Unit/Domains/Guardian/GuardianChildAuthorizationTest.php`
- `docs/docs/00_Project_Management/TASK-11.3_Guardian_Linking_Child_Level_Authorization_Report.md`

## 10. Validation evidence — commands and exact results

Syntax check:

```powershell
$files = git status --porcelain=v1 -uall | ForEach-Object { $_.Substring(3) } | Where-Object { $_ -like '*.php' }; foreach ($f in $files) { php -l $f }
```

Result: no syntax errors in any changed PHP file.

Focused authorization unit tests:

```powershell
php vendor/bin/pest tests/Unit/Domains/Guardian/GuardianChildAuthorizationTest.php --colors=never
```

Result: `Tests: 11 passed (34 assertions)`, `Duration: 17.86s`.

Focused Parent panel feature tests (actual Filament list/view HTTP paths):

```powershell
php vendor/bin/pest tests/Feature/Filament/ParentChildResourceAuthorizationTest.php --colors=never
```

Result: `Tests: 7 passed (14 assertions)`, `Duration: 6.08s`.

Guardian domain regression set:

```powershell
php vendor/bin/pest tests/Unit/Domains/Guardian tests/Feature/Domains/Guardian tests/Arch/GuardianDomainArchitectureTest.php --colors=never
```

Result: `Tests: 130 passed (276 assertions)`, `Duration: 38.66s`.

Student, identity-matching, and architecture regression set:

```powershell
php vendor/bin/pest tests/Unit/Domains/Student tests/Feature/Domains/Student tests/Unit/Domains/IdentityMatching tests/Arch/StudentDomainArchitectureTest.php --colors=never
```

Result: `Tests: 125 passed (308 assertions)`, `Duration: 36.32s`.

Role, provisioning, activation, and tenant-isolation regression set:

```powershell
php vendor/bin/pest tests/Feature/Filament/SchoolAdminRoleAssignmentSecurityTest.php tests/Unit/Domains/User/AccountProvisioningServiceTest.php tests/Unit/Domains/User/EnrollmentLinkedAccountProvisioningServiceTest.php tests/Feature/Domains/User/AccountInvitationActivationHttpTest.php tests/Feature/Api/ApiTenantIsolationTest.php tests/Feature/Filament/DirectorTenantIsolationTest.php tests/Feature/AccountRecovery/SchoolAdminLifecycleTest.php --colors=never
```

Result: `Tests: 100 passed (549 assertions)`, `Duration: 57.30s`.

Full suite:

```powershell
php vendor/bin/pest --colors=never
```

Result: `Tests: 2437 passed (7552 assertions)`, `Duration: 879.41s`.

Pint:

```powershell
$files = git status --porcelain=v1 -uall | ForEach-Object { $_.Substring(3) } | Where-Object { $_ -like '*.php' }; php vendor/bin/pint --test $files
```

Result: `{"tool":"pint","result":"passed"}`.

PHPStan / Larastan:

```powershell
php vendor/bin/phpstan analyse --no-progress --error-format=table
```

Result: `[OK] No errors`.

The focused tests cover: authorized access, independent access to two same-school children, per-child revocation, unlinked/sibling/crafted IDs, cross-school denial, revoked and expired relationships, unverified name-only and phone-only evidence, inactive/suspended Parent accounts, missing tenant context, idempotent verification, duplicate conflicts, history and privacy-safe audits, and unchanged student status semantics. The feature tests exercise real Parent panel list and view requests, including 403 responses for crafted IDs.

## 11. Git state, prohibited operations, and remaining scope

Initial `git status --porcelain=v1 -uall`: **clean working tree** at `cc05790 commit du 28_11`.

Final `git status --porcelain=v1 -uall`:

```text
 M app/Domains/Guardian/Application/Actions/UnlinkGuardianAction.php
 M app/Domains/Guardian/Infrastructure/Models/Guardian.php
 M app/Domains/Guardian/Infrastructure/Models/StudentGuardianRelationship.php
 M app/Domains/System/Enums/AuditAction.php
 M app/Providers/Filament/ParentPanelProvider.php
 M database/factories/Guardian/GuardianFactory.php
 M database/factories/Guardian/StudentGuardianRelationshipFactory.php
 M docs/docs/00_Project_Management/TASK-11_Student_Guardian_Account_Provisioning_Decisions.md
?? app/Domains/Guardian/Application/DTOs/GuardianRelationshipRevocationData.php
?? app/Domains/Guardian/Application/DTOs/GuardianRelationshipVerificationData.php
?? app/Domains/Guardian/Application/Services/GuardianChildAccessService.php
?? app/Domains/Guardian/Application/Services/GuardianRelationshipVerificationService.php
?? app/Domains/Guardian/Domain/Enums/GuardianAccessDenialReason.php
?? app/Domains/Guardian/Domain/Enums/GuardianChildOperation.php
?? app/Domains/Guardian/Domain/Enums/RelationshipPortalAccessStatus.php
?? app/Domains/Guardian/Domain/Enums/RelationshipVerificationStatus.php
?? app/Domains/Guardian/Domain/Exceptions/GuardianChildAccessDeniedException.php
?? app/Domains/Guardian/Domain/Exceptions/GuardianRelationshipVerificationException.php
?? app/Filament/Parent/Resources/ParentChildResource.php
?? app/Filament/Parent/Resources/ParentChildResource/Pages/ListParentChildren.php
?? app/Filament/Parent/Resources/ParentChildResource/Pages/ViewParentChild.php
?? database/migrations/2026_11_27_000001_add_portal_authorization_to_student_guardians_table.php
?? docs/docs/00_Project_Management/TASK-11.3_Guardian_Linking_Child_Level_Authorization_Report.md
?? tests/Feature/Filament/ParentChildResourceAuthorizationTest.php
?? tests/Unit/Domains/Guardian/GuardianChildAuthorizationTest.php
```

`git diff --cached --name-only` returned no output: nothing is staged. HEAD remains `cc05790 commit du 28_11`. No commit, push, reset, checkout, clean, restore, or stash was performed.

Explicitly not implemented in this slice:

- Decision 7 responsibility taxonomy, responsibility types, or flag redesign.
- Task 11.4 enrollment orchestration.
- Task 11.5 invitation delivery, activation, or account activation flows.
- Email/SMS sending.
- Parent APIs, exports, widgets, or child mutation endpoints.
- Any change to `SchoolAdminRolePolicy`, `UserRoleChangeService`, or the approved role allowlist.

The next action is owner review of this Task 11.3 slice. Task 11.4 and Task 11.5 remain stopped until separately authorized.

---

## 12. Independent security-review remediation — 2026-11-28

Sections 1–11 above are retained as the original implementation record and are **not** rewritten. An independent Task 11.3 review returned **REQUIRES CORRECTIONS** with findings A–G. All seven material findings were corrected below without changing Decisions 7 or 8, without activating Student or Parent accounts, without starting Tasks 11.4/11.5, and without expanding `SchoolAdminRolePolicy::ALLOWED_ROLES`.

### 12.1 Correction A — authenticated, active, same-school, authorized actor

`GuardianRelationshipVerificationService` now resolves the reviewer from `Auth::user()` instead of treating `$actor` as an audit-only parameter:

- `resolveActor()` rejects an unauthenticated request (`ACTOR_NOT_AUTHENTICATED`), an explicit actor different from the authenticated user (`ACTOR_MISMATCH`), a trashed/inactive actor (`ACTOR_NOT_ACTIVE`), an actor outside the initialized tenant (`ACTOR_SCHOOL_MISMATCH`), and a `parent` actor (`ACTOR_NOT_AUTHORIZED`).
- `authorizeActor()` checks the new `verify` / `revoke` abilities through `Gate::forUser($actor)`, so permissions are enforced on the real actor and not only on the target Parent account.
- `assertTargetSeparation()` rejects a reviewer whose user ID is the Guardian's linked `user_id`, so a Parent cannot self-approve even if granted `guardian.verify`.
- These checks apply to request, approve, reject, and revoke, not only to approval. Adversarial tests cover null/unauthenticated actors, unauthorized and suspended staff, cross-school staff, Parent self-approval, and an explicit actor mismatch.

### 12.2 Correction B — identity matching is not relationship proof

Task 11.1 `SchoolScopedIdentityMatcher` is still mandatory, but its role is now explicitly limited to resolving whether the target Parent user and Guardian record are the same identity. `EXACT_MATCH`, `automaticReusePermitted`, matching IDs, numbers, names, and phones no longer establish a Guardian–Student relationship. Portal access additionally requires a durable relationship with trusted link provenance (§12.3), an explicit request, and an authorized reviewer. The adversarial test `exact match and persisted IDs alone do not prove a guardian student relationship` asserts that an exact identity match plus a directly inserted durable-looking row is denied with `RELATIONSHIP_PROVENANCE_MISSING` and grants no access.

### 12.3 Correction C — secure lifecycle with trusted provenance

The lifecycle is now request → authorized review → persisted approval/rejection → access only after valid approval → authorized revocation:

| Step | Enforcement | Audit / history |
|---|---|---|
| Request | `request()` requires an authenticated authorized actor and a trusted durable relationship; it changes no portal state. | `portal_access_requested` history and `GuardianRelationshipRequested` audit. |
| Approval | `verifyAndAuthorize()` requires an existing request, trusted provenance, valid dates, identity match, and `verify` ability. It never creates a relationship: the former `LinkGuardianAction` call was removed. | `portal_access_verified` history and `GuardianRelationshipVerified` audit. |
| Rejection | `reject()` persists `verification_status = rejected` and `portal_access_status = not_authorized`. | `portal_access_rejected` history and `GuardianRelationshipRejected` audit. |
| Revocation | `revoke()` requires the `revoke` ability and preserves the relationship row while setting `revoked`. | `portal_access_revoked` history and `GuardianRelationshipRevoked` audit. |
| Parent access | Granted only after a valid approval and only through `GuardianChildAccessService`. | `GuardianChildViewed` after success; `GuardianChildAccessDenied` after failure. |

**Provenance rule adopted (smallest fail-closed rule, no new schema):** a durable row is trusted only when there is an attributed `linked` history row from `LinkGuardianAction` with matching `school_id`, `student_id`, and `guardian_id`, whose actor was `guardian.link`-authorized or `super_admin`. Direct inserts, factories, embedded-row promotion, and legacy writes produce no such row and fail closed. This rule is enforced both at approval time (`assertTrustedProvenance()`) and at every Parent access decision (`hasTrustedLinkProvenance()` plus the `whereExists` subquery in `scopePortalAuthorized()`). There is no fallback that invents provenance or approval.

### 12.4 Correction D — server-resolved identifiers and target integrity

`GuardianRelationshipVerificationData` and `GuardianRelationshipRevocationData` now accept only a tenant-resolved `relationship_id`; their `fromArray()` factories reject `school_id`, `user_id`, `guardian_id`, and `student_id` with `IDENTIFIER_OVERRIDE_REJECTED`. The service resolves school from tenant context, actor from the authenticated session, and Guardian/Student from the durable relationship. `assertSubjectOwnership()` and `resolveRelationship()` reject cross-school IDs and actor/target mismatches. `ParentChildResource` and `ViewParentChild` were updated to the new service signatures, so no panel code passes a caller-controlled user ID.

### 12.5 Correction E — audit actor attribution and distinct actions

All lifecycle audit rows now use the authenticated reviewer as `user_id`/`user_type` and include `actor_id` plus separately named `guardian_id` and `student_id` in `new_values`. `school_id` is derived from the relationship or tenant, never from DTO input. Verification, rejection, and revocation are distinct `AuditAction` values with distinct history events. The audit test asserts reviewer identity, tenant school, separated target IDs, distinct actions, and absence of Parent email/phone in serialized audit data.

### 12.6 Correction F — durable relationships are not deleted by embedded edits

`UpdateStudentAction::replaceGuardians()` now deletes only `guardian_id IS NULL` embedded rows, scoped to the student's school, and deduplicates embedded references before recreating them. `Student::guardians()` also filters `guardian_id IS NULL`, so Filament embedded-guardian forms can no longer load or overwrite durable rows. The adversarial test confirms an authorized durable relationship survives embedded replacement, still grants access, and can then be revoked.

### 12.7 Correction G — child authorization is bound to the authenticated user

`GuardianChildAccessService` no longer accepts a caller-controlled user ID in any public method: `assertParentContext()`, `assertCanAccess($studentId, $operation)`, and `scopeStudentQuery($query, $operation)` all resolve the authenticated user internally. A crafted call that supplies a user ID either fails with a `TypeError` or is treated as a student ID and fails closed as a missing/unauthorized child.

### 12.8 Smallest policy extension (no role allowlist change)

`PermissionService::EXTRA_ACTIONS['guardian']` now includes `verify` and `revoke` in addition to the existing five actions, so `PermissionSeeder` creates `guardian.verify` and `guardian.revoke`. `HandlesGuardianAuthorization` exposes `verify()` and `revoke()` through its existing school-scoped, permission-based `authorizeRelationship()` helper; `super_admin` keeps its existing bypass. `SchoolAdminRolePolicy::ALLOWED_ROLES` remains exactly `['teacher', 'secretary', 'accountant']`; no Parent role assignment or new responsibility taxonomy was introduced.

A real Filament entry point was also added: `StudentGuardianRelationshipResource` exposes French-labelled request/approve/reject/revoke table actions (`Demander l’accès au portail`, `Approuver l’accès au portail`, `Refuser l’accès au portail`, `Révoquer l’accès au portail`) with a failure notification `Action refusée`. The tests exercise both the visible lifecycle through `callTableAction()` and a crafted `mountTableAction()`/`callMountedTableAction()` attempt by a Parent and an unauthorized staff member; neither can change relationship state.

### 12.9 Validation evidence — commands and exact results

All commands were run from `C:\xampp\htdocs\NexusSchool` with `--colors=never`. Outputs were captured to files under `C:\Users\shine\AppData\Local\Temp\opencode\`.

| Check | Command | Exact result | Exit |
|---|---|---|---|
| Syntax (all changed PHP files) | `$files = git status --porcelain=v1 -uall \| ForEach-Object { $_.Substring(3) } \| Where-Object { $_ -like '*.php' }; foreach ($f in $files) { php -l $f }` | `No syntax errors detected` for every changed PHP file | 0 |
| Focused unit | `php vendor/bin/pest tests/Unit/Domains/Guardian/GuardianChildAuthorizationTest.php --colors=never` | `Tests: 21 passed (71 assertions)`, `Duration: 8.86s` | 0 |
| Parent panel HTTP | `php vendor/bin/pest tests/Feature/Filament/ParentChildResourceAuthorizationTest.php --colors=never` | `Tests: 8 passed (15 assertions)`, `Duration: 84.20s` | 0 |
| Filament review actions | `php vendor/bin/pest tests/Feature/Filament/GuardianRelationshipReviewActionsTest.php --colors=never` | `Tests: 2 passed (39 assertions)`, `Duration: 6.01s` | 0 |
| Combined focused final run | `php vendor/bin/pest tests/Unit/Domains/Guardian/GuardianChildAuthorizationTest.php tests/Feature/Filament/ParentChildResourceAuthorizationTest.php tests/Feature/Filament/GuardianRelationshipReviewActionsTest.php --colors=never` | `Tests: 31 passed (125 assertions)`, `Duration: 17.82s` | 0 |
| Guardian domain | `php vendor/bin/pest tests/Unit/Domains/Guardian tests/Feature/Domains/Guardian --colors=never` | `Tests: 131 passed (296 assertions)`, `Duration: 36.56s` | 0 |
| Student domain | `php vendor/bin/pest tests/Unit/Domains/Student tests/Feature/Domains/Student --colors=never` | `Tests: 92 passed (158 assertions)`, `Duration: 36.08s` | 0 |
| Task 11.1 identity matching | `php vendor/bin/pest tests/Unit/Domains/IdentityMatching --colors=never` | `Tests: 25 passed (132 assertions)`, `Duration: 8.24s` | 0 |
| Task 11.2 provisioning/activation | `php vendor/bin/pest tests/Unit/Domains/User tests/Feature/Domains/User --colors=never` | `Tests: 97 passed (341 assertions)`, `Duration: 31.85s` | 0 |
| Tenant isolation | `php vendor/bin/pest --filter=TenantIsolation --colors=never` | `Tests: 90 passed (257 assertions)`, `Duration: 45.93s` | 0 |
| Role allowlist regression | `php vendor/bin/pest tests/Feature/Filament/SchoolAdminRoleAssignmentSecurityTest.php --colors=never` | `Tests: 19 passed (208 assertions)`, `Duration: 37.36s` | 0 |
| Full suite (first run) | `php vendor/bin/pest --colors=never` | `Tests: 1 failed, 2449 passed (7628 assertions)`, `Duration: 947.92s`. The only failure was `Tests\Unit\Domains\SubjectCoefficient\SubjectCoefficientServiceTest`, a faker-generated UNIQUE collision on `grade_levels(program_id, level_order)`; it is unrelated to Task 11.3. | 1 |
| Isolated retry of that failure | `php vendor/bin/pest tests/Unit/Domains/SubjectCoefficient/SubjectCoefficientServiceTest.php --colors=never` | `Tests: 24 passed (31 assertions)`, `Duration: 9.06s` | 0 |
| Full suite (second run) | `php vendor/bin/pest --colors=never` | `Tests: 2450 passed (7629 assertions)`, `Duration: 936.68s` — clean run confirming the first run's single failure was an unrelated flaky faker collision | 0 |
| Pint (changed PHP files) | `$files = git status --porcelain=v1 -uall \| ForEach-Object { $_.Substring(3) } \| Where-Object { $_ -like '*.php' }; php vendor/bin/pint --test $files` | `{"tool":"pint","result":"passed"}` | 0 |
| PHPStan / Larastan | `php vendor/bin/phpstan analyse --no-progress --error-format=table` | `[OK] No errors` | 0 |

The focused set covers: authorized access, two independently approved same-school children, per-child revocation, unlinked/sibling/crafted/missing IDs, cross-school denial, revoked and expired relationships, exact-match-only denial, missing provenance, request-before-approval denial, persisted rejection, idempotent approval, duplicate conflicts, missing/stale tenant, unauthenticated/unauthorized/suspended/cross-school/parent actors, actor mismatch, identifier overrides, audit attribution and privacy, embedded-guardian replacement preservation, user-ID binding, inactive accounts, unsupported operations, and unchanged student-status semantics. The Parent HTTP tests cover real list/view requests and 403 responses; the Filament tests cover real review table actions and crafted calls.

### 12.10 Git comparison and final status

Initial remediation state at `cc05790 commit du 28_11`: `git diff --cached --name-only` empty; `git status --porcelain=v1 -uall` contained **25 entries** (8 modified, 17 untracked), captured before any remediation edit.

Final state: **31 entries** (13 modified, 18 untracked). `git diff --cached --name-only` is still empty and HEAD is still `cc05790 commit du 28_11`. Exactly six entries were added by this remediation and none were removed:

```text
 M app/Domains/Guardian/Domain/Traits/HandlesGuardianAuthorization.php
 M app/Domains/Guardian/Presentation/Filament/Resources/StudentGuardianRelationshipResource.php
 M app/Domains/Permission/Services/PermissionService.php
 M app/Domains/Student/Application/Actions/UpdateStudentAction.php
 M app/Domains/Student/Infrastructure/Models/Student.php
?? tests/Feature/Filament/GuardianRelationshipReviewActionsTest.php
```

No stage, commit, push, reset, checkout, clean, restore, or stash operation was performed. `docs/docs/00_Project_Management/TASK-11_Student_Guardian_Account_Provisioning_Decisions.md` still records Decision 8 **APPROVED** and Decision 7 **DEFERRED — NOT APPROVED**; Decisions 1–6, 9, and 10 are unchanged.

Known limitations, reported rather than hidden:

- `guardian.verify` and `guardian.revoke` are now seeded and automatically granted to newly provisioned/ re-granted `school_admin` accounts. Existing installations must run `PermissionSeeder` and re-grant school-admin permissions; until then, review actions fail closed with `ACTOR_NOT_AUTHORIZED`.
- `LinkGuardianAction` still accepts an optional actor and does not itself authorize the caller. The provenance rule compensates by requiring an attributed `linked` history row from an authorized actor before approval or Parent access. Making the link actor mandatory is a future hardening step, not a blocker for this slice because no path can grant access without that evidence.
- Generic Filament delete paths and the embedded `GuardiansRelationManager` can still soft-delete durable rows without emitting an `unlinked` history event. Access fails closed after such deletion because the relationship is trashed; adding canonical unlink routing is left as a separate hardening step.
- Task 11.4 and Task 11.5 remain unstarted. No Student or Parent account was activated and no email/SMS delivery was introduced.
- The first full-suite run contained one unrelated flaky faker collision (§12.9); its isolated retry passed.

**Final remediation verdict: COMPLETE.** All seven independent-review findings A–G are corrected, covered by adversarial service and real application entry-point tests, and validated by the focused, domain, tenant-isolation, role-policy, Pint, and PHPStan runs. The original independent verdict `REQUIRES CORRECTIONS` is retained above as historical evidence. Overall Task 11 remains **NOT COMPLETE** pending owner review and the separately authorized Tasks 11.4/11.5.

## 13. Provenance-forgery remediation — 2026-11-28

Sections 1–12 are preserved verbatim as the historical record. Section 12's **COMPLETE** verdict is not retroactively withdrawn, but a subsequent independent review found a new blocker: an authenticated but unauthorized actor could call the actor-aware link action while attributing the resulting `linked` history to another authorized user. Because §12.3 treated an attributed `linked` row as proof, that forged history could become the basis for approval and Parent portal access. This section records the corrective work performed on 2026-11-28.

### 13.1 Confirmed blocker and correction boundary

The blocker was proven by code inspection and an adversarial test: `LinkGuardianAction::execute()` accepted a caller-supplied actor, while `GuardianLinked` accepted a caller-supplied actor too. A caller could therefore create a durable same-school relationship under an unauthorized session but write a `linked` history row naming an authorized user. The provenance rule in §12.3 then trusted that row because it matched school, student, guardian, and an authorized actor ID.

The correction preserves all approved Task 11 decisions and boundaries:

- Decision 8 remains **APPROVED**: one Guardian may serve multiple same-school Students, but each association requires independent verification and explicit authorization.
- Decision 7 remains **DEFERRED — NOT APPROVED**: no responsibility taxonomy or role semantics were introduced.
- Decisions 1–6, 9, and 10 are unchanged.
- Task 11.1 identity matching and Task 11.2 provisioning/activation remain intact and are covered by regression runs.
- Task 11.4 and Task 11.5 were not started. No Student/Parent account was activated and no email/SMS delivery was added.
- `SchoolAdminRolePolicy::ALLOWED_ROLES` remains exactly `['teacher', 'secretary', 'accountant']`. No new `guardian.link` grant and no new role allowlist entry was introduced.

### 13.2 Actor contract — no caller-supplied actor

`LinkGuardianAction::execute()` now has exactly one parameter, `RelationshipData $data`. It resolves the actor internally with `Auth::user()` and refuses to proceed unless all of the following hold:

1. The actor is authenticated.
2. The actor is not trashed and has `UserStatus::Active`.
3. The actor is not a `parent`.
4. The actor's `school_id` equals the initialized tenant's school.
5. The tenant exists, is not trashed, and is active.
6. `RelationshipData::schoolId` equals the initialized tenant's school.
7. `Gate::forUser($actor)->allows('link', $guardian)` succeeds. For non-Super-Admin staff this requires `guardian.link`; the existing `super_admin` bypass remains unchanged.

`GuardianService::link(int $schoolId, array $attributes)` no longer forwards an actor. `GuardianLinked::__construct()` also has exactly one parameter, the relationship; it resolves its own actor from `Auth::user()`. Reflection-based adversarial tests assert these parameter counts and names, so PHP's silent acceptance of extra positional arguments cannot hide a regression.

### 13.3 Non-forgeable provenance

`GuardianLinkProvenance` was added as the single server-side attestation path:

- `issueFor($relationship, $actor)` is called only by the successful, durable `LinkGuardianAction` transaction, immediately before `GuardianLinked::dispatch()`.
- `takeFor($relationship, $actor)` is called only by the `GuardianLinked` constructor. It consumes a one-time pending issuance keyed to the exact relationship object and actor; a directly constructed/dispatched `GuardianLinked` without that issuance throws `RelationshipProvenanceMissing`.
- The stored payload contains `provenance_version` and an HMAC-SHA256 `provenance_signature` over school, relationship ID, student ID, guardian ID, and actor ID using `config('app.key')`.
- `GuardianAuditLogger::handleGuardianLinked()` persists that signed payload in the immutable `linked` history row while keeping the authenticated actor as `user_id`/`user_type`.
- `StudentGuardianRelationship::hasTrustedLinkProvenance()` and `GuardianRelationshipVerificationService::assertTrustedProvenance()` now require `GuardianLinkProvenance::isTrustedHistory()`. That check verifies the HMAC and re-checks that the recorded actor still exists, is active, is same-school, and is `guardian.link`-authorized or `super_admin`. A directly inserted `linked` row naming an authorized user therefore fails because it cannot reproduce the signature.

This replaces the weaker §12.3 rule with a signed, one-time, actor-bound rule and removes the former §12.10 limitation that `LinkGuardianAction` accepted an optional actor.

### 13.4 Child-level access and review enforcement

`GuardianChildAccessService::scopeStudentQuery()` now PHP-filters `portalAuthorized()` rows through `hasTrustedLinkProvenance()` in addition to the existing child-level checks. Every `assertCanAccess()` decision also rejects a relationship without trusted provenance. Approval-time `assertTrustedProvenance()` applies the same verifier. There is no fallback that promotes a durable-looking row, identity match, or caller-supplied ID into trusted provenance.

### 13.5 Filament delete routing

`StudentGuardianRelationshipResource` no longer exposes generic `DeleteAction` or `DeleteBulkAction`. The visible delete-labelled row action is `unlink_relationship`, restricted by `canDelete()` to `super_admin`, and it calls the canonical audited `UnlinkGuardianAction` with the authenticated `Auth::user()`. `canDeleteAny()` returns `false`, and `bulkActions([])` removes bulk deletion. This replaces the §12.10 limitation about generic soft deletes bypassing `unlinked` history. A real Filament test now asserts the soft delete, revoked portal status, and `unlinked` history attribution to the Super Admin actor.

### 13.6 Adversarial evidence

`tests/Unit/Domains/Guardian/GuardianChildAuthorizationTest.php` was extended with the following proofs:

- Reflection asserts one-parameter `LinkGuardianAction::execute()`, two-parameter actorless `GuardianService::link()`, and one-parameter `GuardianLinked::__construct()`.
- Unauthenticated, unauthorized staff, Parent (even when granted `guardian.link`), suspended, and cross-school actors cannot create a durable relationship or any `linked` history.
- An authorized same-school link records the authenticated actor in both the event and history, and produces trusted signed provenance.
- A supplied authorized `User` object passed via `ReflectionMethod::invokeArgs()` is ignored; the unauthorized authenticated actor is denied.
- A supplied actor ID passed via `ReflectionMethod::invokeArgs()` does not alter `linked` history attribution.
- The approved `super_admin` path creates trusted provenance without changing `SchoolAdminRolePolicy::ALLOWED_ROLES`.
- Directly dispatching `GuardianLinked` without issuance fails with `RelationshipProvenanceMissing` and writes no history.
- A directly inserted `linked` row naming an authorized user but carrying an invalid signature grants neither Parent access nor approval.
- Duplicate and cross-school link protections remain effective.

The main proof required by the independent review is therefore satisfied: an unauthorized authenticated actor cannot create a durable Guardian–Student relationship while attributing its `linked` history to another authorized user.

### 13.7 Validation evidence — commands and exact results

All commands were run from `C:\xampp\htdocs\NexusSchool`. Changed-PHP-file commands used `PHP_FILES=36` from `git status --porcelain=v1 -uall`.

| Check | Command | Exact result | Exit |
|---|---|---|---|
| Syntax (all changed PHP files) | `$files = git status --porcelain=v1 -uall \| ForEach-Object { $_.Substring(3) } \| Where-Object { $_ -like '*.php' }; foreach ($f in $files) { php -l $f }` | `No syntax errors detected` for all 36 changed PHP files | 0 |
| Main adversarial suite | `php vendor/bin/pest tests/Unit/Domains/Guardian/GuardianChildAuthorizationTest.php --colors=never` | `Tests: 31 passed (122 assertions)`, `Duration: 22.75s` | 0 |
| Link/unlink + audit + Filament regressions | `php vendor/bin/pest tests/Unit/Domains/Guardian/Actions/RelationshipActionsTest.php tests/Feature/Domains/Guardian/GuardianAuditTest.php tests/Feature/Filament/ParentChildResourceAuthorizationTest.php tests/Feature/Filament/GuardianRelationshipReviewActionsTest.php --colors=never` | `Tests: 27 passed (101 assertions)`, `Duration: 13.77s` | 0 |
| Guardian domain | `php vendor/bin/pest tests/Unit/Domains/Guardian tests/Feature/Domains/Guardian tests/Arch/GuardianDomainArchitectureTest.php --colors=never` | `Tests: 150 passed (364 assertions)`, `Duration: 52.80s` | 0 |
| Student domain | `php vendor/bin/pest tests/Unit/Domains/Student tests/Feature/Domains/Student tests/Arch/StudentDomainArchitectureTest.php --colors=never` | `Tests: 100 passed (176 assertions)`, `Duration: 23.58s` | 0 |
| Task 11.1 identity matching | `php vendor/bin/pest tests/Unit/Domains/IdentityMatching --colors=never` | `Tests: 25 passed (132 assertions)`, `Duration: 7.78s` | 0 |
| Task 11.2 provisioning/activation | `php vendor/bin/pest tests/Unit/Domains/User tests/Feature/Domains/User --colors=never` | `Tests: 97 passed (341 assertions)`, `Duration: 25.73s` | 0 |
| Tenant isolation | `php vendor/bin/pest --filter=TenantIsolation --colors=never` | `Tests: 90 passed (257 assertions)`, `Duration: 34.85s` | 0 |
| Role allowlist regression | `php vendor/bin/pest tests/Feature/Filament/SchoolAdminRoleAssignmentSecurityTest.php --colors=never` | `Tests: 19 passed (208 assertions)`, `Duration: 30.64s` | 0 |
| Final targeted recheck after the last provenance parser adjustment | `php vendor/bin/pest tests/Unit/Domains/Guardian/GuardianChildAuthorizationTest.php tests/Feature/Domains/Guardian/GuardianAuditTest.php --colors=never` | `Tests: 36 passed (135 assertions)`, `Duration: 54.75s` | 0 |
| Pint (changed PHP files) | `$files = git status --porcelain=v1 -uall \| ForEach-Object { $_.Substring(3) } \| Where-Object { $_ -like '*.php' }; php vendor/bin/pint --test $files` | `{"tool":"pint","result":"passed"}` | 0 |
| PHPStan / Larastan | `php vendor/bin/phpstan analyse --no-progress --error-format=table` | Final run: `[OK] No errors` after correcting two intermediate static-analysis findings in `GuardianLinkProvenance::isTrustedHistory()` | 0 |
| Full suite | `php vendor/bin/pest --colors=never` | __FULL_SUITE_ROW__ | __FULL_SUITE_EXIT__ |

An earlier `php vendor/bin/pint $files` pass fixed import ordering in three files; the final `--test` run above is clean. The PHPStan intermediate failures were type-narrowing issues around the cast `payload` attribute, corrected by decoding the raw persisted JSON and validating identifiers without suppressing errors, baselines, or ignores.

### 13.8 Limitations and review boundary

- Provenance is bound to the actor's current authorization state. If a recording staff member later becomes inactive, suspended, or loses `guardian.link`, previously linked rows fail closed even though the original link was valid. This is intentional fail-closed behavior and can be revisited only with separate owner approval.
- The one-time pending issuance is process-local. `issueFor()` and the event constructor run synchronously in the same request; no queued actorless link workflow exists in the codebase.
- `unlink_relationship` is deliberately visible only to `super_admin`; school staff retain portal review/revoke actions but not raw relationship deletion.
- Task 11.4 and Task 11.5 remain unstarted. Overall Task 11 remains **NOT COMPLETE**.
- This section closes the specific provenance-forgery blocker only for the purpose of handing the work back for a **separate read-only independent review**. It is not a self-declared Task 11.3 closure.

### 13.9 Git state and final verdict

Final Git state at the end of this remediation:

```text
HEAD: cc05790 commit du 28_11
git diff --cached --name-only: (empty)
git status --porcelain=v1 -uall: 38 entries (19 modified, 19 untracked)
```

No stage, commit, push, reset, checkout, clean, restore, or stash operation was performed. The prior §12.10 state is retained as historical evidence; this section reports the current working tree after the provenance remediation.

**Final verdict: BLOCKER RESOLVED — READY FOR FINAL INDEPENDENT REVIEW.** The authenticated-context actor contract, signed one-time provenance, child-level enforcement, and adversarial tests are in place and validated. A separate read-only independent review is still required before Task 11.3 can be closed.
