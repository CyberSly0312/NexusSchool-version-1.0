# TASK-11 — Student & Guardian Account Provisioning — Phase 0 Business and Architectural Decisions

| Field | Value |
|---|---|
| Status | **PARTIAL OWNER APPROVAL — TASK 11.1 IMPLEMENTED — TASK 11.2 IMPLEMENTATION IN PROGRESS — OVERALL TASK 11 NOT COMPLETE** |
| Date | 2026-11-27 |
| Verified HEAD | `daf4b71 account management` |
| Scope | Ten business decisions, identity/relationship policies, architecture guidance, and the approved Task 11.1/11.2 implementation slices. Decisions 7 and 8 remain pending. |
| Application code modified by this task | Task 11.1 identity matching and Task 11.2 approved provisioning foundation. |
| Migrations, tests, configuration, routes modified by this task | No migrations were added. Focused tests were added for the approved slices. |
| Only file created by this task | `docs/docs/00_Project_Management/TASK-11_Student_Guardian_Account_Provisioning_Decisions.md` |
| Baseline document | `docs/docs/00_Project_Management/TASK-09_Student_Guardian_Account_Provisioning_Discovery_Report.md` — verified as **DRAFT DISCOVERY — DOCUMENTATION ONLY — NO IMPLEMENTATION, NO GOVERNANCE APPROVAL** |
| Related non-authoritative document | `docs/docs/00_Project_Management/Account_Provisioning_Foundation_Specification.md` — DRAFT, not approved |
| Approval state | **Owner-approved:** Decisions 1, 2, 3, 4, 5, 6, 9 and 10. **Pending:** Decisions 7 and 8. The overall Task 11 workstream is not approved or complete. |

## Owner governance update — 2026-11-27

The project owner explicitly approved the following decisions on **2026-11-27** for Task 11.2 implementation:

| Decision | Exact decision reference | Approval recorded |
|---|---|---|
| Decision 1 | `### Decision 1 — Should every student have a User account?` | Approved for Task 11.2: student accounts are optional and require an explicit school request and eligibility/contact evidence. |
| Decision 2 | `### Decision 2 — When should a guardian receive an account invitation?` | Approved for Task 11.2: guardian invitations may only be initiated after enrollment confirmation and an explicit portal-access request for the specific guardian. |
| Decision 5 | `### Decision 5 — What happens when a matching User already has a Pending account and an outstanding invitation?` | Approved for Task 11.2: reuse an existing usable invitation of the same purpose; do not create duplicate active invitations; revoke an outstanding invitation before issuing a different-purpose invitation where permitted. |
| Decision 6 | `### Decision 6 — What happens when an invitation has expired?` | Approved for Task 11.2: expired invitations preserve User/Guardian history and may be reissued by an authorized actor using the existing 48-hour validity period. |
| Decision 10 | `### Decision 10 — What should School Admins be allowed to do?` | Approved for Task 11.2: School Admin account operations remain narrowly scoped to authorized enrollment-linked workflows and must not bypass tenant isolation, `SchoolAdminRolePolicy`, or `UserRoleChangeService`. |

Decisions 3, 4, and 9 were already approved and implemented in Task 11.1. Their decision text is intentionally unchanged by this update. Decisions 7 and 8 remain pending owner approval. The overall Task 11 workstream is not approved or complete.

---

## Legend and reading rules

- **Verified current behavior** — a fact read directly from the current repository during this phase.
- **Proposed recommendation** — a governance recommendation prepared for the owner; not effective until approved.
- **Decision requiring owner approval** — a recommendation that must be explicitly accepted or rejected before implementation.
- **Out of scope** — deliberately excluded from Phase 0 and from the proposed V1 unless separately approved.
- The report language is English. Any proposed user-facing application text or example is written in French.

---

## 1. Executive summary

Task 09 established that NexusSchool can already create users, issue single-use invitations, activate pending accounts, enroll students, register guardians, and link guardians to students. It also established that these capabilities are not connected: the canonical enrollment path creates only an `Enrollment` aggregate and never creates or reuses a User account or invitation.

This phase verified the Task 09 findings against the current code and found that the core discovery remains accurate. Two areas are stale and were corrected here from code:

1. Task 09 section 8 describes `RolesRelationManager` as an unguarded role association surface. Current code has been hardened by Task 10B: it uses `SchoolAdminRolePolicy::ALLOWED_ROLES`, `UserRoleChangeService`, tenant checks, and audited role events.
2. Task 09 describes the invitation link as a persistent Filament notification. That is still true for the school staff provisioning page, but Task 10C added a separate central capability-delivery page that places a one-time token in a URL fragment and immediately removes it from browser history. No email or SMS provider is assumed in either path.

The ten recommended V1 policies are conservative: enrollment must never depend on account creation; student accounts are opt-in for schools with a genuine need and a dedicated contact; guardian invitations are triggered only by explicit school request; existing accounts and Guardian records are reused only on strong, school-scoped evidence; name or unverified phone resemblance is never enough to link an account; multiple guardians and siblings remain supported through durable `student_guardians` relationships; School Admins cannot assign arbitrary roles or bypass the centralized role service; global email uniqueness and tenant isolation are preserved.

The most important unresolved risks are child-level data authorization in the student/parent panels, the absence of a manual review queue for ambiguous matches, the absence of an email/SMS delivery provider, duplicate Student/Guardian Eloquent stacks, and the legacy enrollment resource that still writes a different table and could bypass any future orchestration.

---

## 2. Verified current-state findings

### 2.1 Baseline and evidence status

**Verified current behavior**

- The complete Task 09 report was read. Its status is explicitly a draft discovery report with no governance approval.
- The `Account_Provisioning_Foundation_Specification.md` is also a draft and is not a decision source.
- `git log -1 --oneline` returned `daf4b71 account management`.
- The working tree already contained Task 10C changes before Task 11 started. They were preserved unchanged.

### 2.2 Account provisioning and invitations

**Verified current behavior**

- `app/Domains/User/Services/AccountProvisioningService.php` normalizes the email with lowercase and trim, requires an active actor, validates the target school and active school state, checks that no active or soft-deleted User uses the email, creates a new `User` with a random unusable password and `Pending` status, assigns one role, issues one invitation, and commits the user/role/invitation in one database transaction.
- School-level provisioning allows only `teacher`, `secretary`, and `accountant`, sourced from `SchoolAdminRolePolicy::ALLOWED_ROLES`. Platform provisioning allows only `school_admin`. `super_admin` is rejected everywhere.
- `purposeForRole()` maps `school_admin` to `school_admin_initial`, `student` to `student_initial`, `parent` to `guardian_initial`, and other staff roles to `staff_initial`. However, `student` and `parent` are not currently allowed roles, so their purposes cannot be reached through this service.
- `app/Models/UserInvitation.php` defines `school_admin_initial`, `school_admin_reset`, `staff_initial`, `guardian_initial`, and `student_initial`. The default expiry is 48 hours; `school_admin_reset` deliberately uses 24 hours elsewhere in Task 10C code.
- Invitation tokens are 64 hexadecimal characters from `random_bytes(32)`. Only `token_hash = sha256(token)` is persisted. The plaintext token exists only in `UserInvitationIssue`.
- `UserInvitation::issue()` revokes prior unconsumed, unrevived invitations for the same `user_id + school_id + purpose` before creating the replacement. The invitations table has a unique index on `token_hash` but no database uniqueness on the purpose tuple.
- `app/Domains/User/Services/AccountInvitationActivationService.php` accepts only initial purposes plus guardian/student purposes, locks the invitation and user rows in a transaction, verifies host school, purpose, expiry/revocation/consumption, `Pending` user state, user/invitation/school school-ID equality, and school existence. It writes a hashed password, activates the user, and consumes the invitation only after a final usable-state recheck.
- Invitation activation is rate-limited by `UserServiceProvider`: 60 requests per minute per IP and 10 per minute per token digest.
- School staff provisioning still returns the activation link through a Filament notification in `app/Filament/Resources/UserResource/Pages/CreateUser.php`; the French text explicitly states that no email or SMS was sent and that the link must be transmitted securely.
- Task 10C central password reset/replacement uses a different one-time delivery mechanism: a central page receives the secret in a URL fragment, the browser does not send the fragment to the server, client-side JavaScript displays the final tenant link, and `history.replaceState()` removes the fragment. No email or SMS provider is involved.
- No invitation email or SMS delivery abstraction is configured in the inspected invitation flows. No such provider is assumed by this report.

### 2.3 Enrollment, Student, Guardian, and relationships

**Verified current behavior**

- The canonical enrollment path is `CreateEnrollment` Filament page → `EnrollmentService::enroll()` → `CreateEnrollmentAction::execute()` → `EnrollmentCreated` → audit/history listeners.
- `CreateEnrollmentData` contains school, student, academic year/program/level/class/section, enrollment number, date, type, notes, and active status. It contains no User, email, phone, password, invitation, guardian account, or portal-access field.
- `CreateEnrollmentAction` validates academic references and student ownership of the school, prevents duplicate active enrollment and duplicate enrollment number, creates only `Enrollment`, and dispatches `EnrollmentCreated`.
- No enrollment, student creation, or guardian creation path creates or reuses a User or a `UserInvitation`.
- Two Eloquent stacks exist over the same physical tables: DDD models under `App\Domains\Student\Infrastructure\Models\*` and `App\Domains\Guardian\Infrastructure\Models\*`, plus legacy models under `App\Models\*`.
- Canonical application actions use the DDD models. `App\Domains\Student\Application\Services\StudentService` and `App\Domains\Guardian\Application\Services\GuardianService` are facades over those application actions.
- Legacy `App\Domains\Student\Services\StudentService` and `App\Domains\Guardian\Services\GuardianService` use `App\Models\Student` and `App\Models\Guardian`.
- `GuardianReference` is a value object with `name`, `phone`, `email`, `relationship`, and `isPrimary`. It contains no Guardian ID and no User ID.
- `CreateStudentAction::syncGuardians()` writes embedded `student_guardians` rows through `App\Domains\Student\Infrastructure\Models\StudentGuardian` with `guardian_id = NULL`.
- `UpdateStudentAction::replaceGuardians()` soft-deletes and recreates those embedded rows when `replaceGuardians` is true. This can discard contact snapshots and any unmirrored relationship data.
- `app/Domains/Guardian/Application/Actions/CreateGuardianAction.php` checks school-scoped duplicates for `guardian_number`, `phone_number`, and `email`, and separately rejects a `user_id` already linked to any Guardian. That `user_id` check is not school-scoped.
- `app/Domains/Guardian/Application/Services/GuardianService::create(int $schoolId, ...)` does not use the `$schoolId` argument; it builds its DTO from the submitted attributes. The Filament creation page passes the tenant ID, but the action relies on the DTO contents.
- `app/Domains/Guardian/Application/Actions/LinkGuardianAction.php` resolves Guardian, Student, and RelationshipType only inside the current school, rejects an existing Guardian–Student pair, clears previous primary when a new primary is assigned, copies Guardian contact details into the relationship, and writes all responsibility flags.
- `student_guardians` supports `guardian_id`, `relationship_type_id`, `is_primary`, `is_emergency_contact`, `is_authorized_pickup`, `is_financial_responsible`, `is_notification_recipient`, `is_medical_contact`, emergency priority, pickup date windows, relationship start/end dates, notes, and soft deletes. The unique index is `(school_id, guardian_id, student_id, deleted_at)`. Because `guardian_id` is nullable, embedded reference rows do not receive the same uniqueness guarantee under common SQL NULL semantics.
- `student_guardian_relationship_history` is an append-only business history table keyed by `student_guardian_id`, with school ID, actor, event, description, payload, and occurrence time.
- Relationship type labels are configured in `config/nexus-guardian.php` and materialized per tenant by `RelationshipTypeSeeder`. The catalog includes family, legal, financial, emergency, and other types. Schools may customize the per-tenant table.

### 2.4 Authorization, role management, and tenant isolation

**Verified current behavior**

- `PanelAccessRegistry` maps `student` → student panel and `parent` → parent panel. Access requires an active user, a mapped role, and an active school. `super_admin` is restricted to the central panel.
- `User::canAccessPanel()` delegates to `PanelAccessRegistry`. `SchoolGatewayAuthController::enterPanel()` rechecks the selection server-side.
- `SchoolAdminRolePolicy::ALLOWED_ROLES` contains only `teacher`, `secretary`, and `accountant`. Labels are French: `Enseignant`, `Secrétaire`, `Comptable`.
- `UserRoleChangeService` is the authoritative server-side path for assigning/removing roles on an existing user. It re-resolves target, school, and roles from persisted identifiers, rejects browser-supplied school/role manipulation, applies the school-admin allowlist, protects self-removal and the last active `school_admin`, and dispatches audited role events.
- Current `RolesRelationManager` is hardened by Task 10B. It uses `UserRoleChangeService::assertTargetIsAccessible()` on mount and authorization, filters role options with `SchoolAdminRolePolicy`, validates before attach, applies through the service for attach/detach/bulk detach, and emits audited role events. Task 09 section 8 is stale on this point.
- `AccountProvisioningService` assigns the fixed role for a newly created account; it is not used for arbitrary role changes on existing accounts.
- No verified School Admin UI currently revokes invitations or suspends/deactivates User accounts. The user list can display suspended status, but no dedicated School Admin suspension action was found for this scope.
- `UserResource` applies tenant scoping and fails closed with `whereRaw('1 = 0')` when no tenant is initialised. `ScopedByTenant` provides the same fail-closed behavior.
- `HasSchoolId` sets `school_id` on creation only when empty and a tenant exists; it is not a global Eloquent scope. Queries must explicitly use `forSchool()` or a tenant-scoped resource.
- `legacy App\Providers\EventServiceProvider` is not registered in `bootstrap/providers.php`. Domain service providers are registered and wire their own canonical audit listeners.
- Student, Guardian, Enrollment, User, and UserInvitation records carry `school_id`. Invitation activation validates host school, invitation school, and user school together.
- `users.email` has a database-unique index. `AccountProvisioningService` checks with trashed users included, so an email used by a soft-deleted account is a conflict. Cross-school email reuse is a conflict, not a match.

### 2.5 Differences from Task 09 that matter for this phase

**Verified current behavior**

- Task 09's core findings remain valid: no account creation in enrollment; no fuzzy matching; no review queue; no provider-based delivery; global email uniqueness; duplicate model stacks; embedded guardian references; and no automatic guardian account linking.
- Task 09's `RolesRelationManager` gap is no longer current. The relation manager is now routed through `UserRoleChangeService`, with allowlist, tenant checks, invariants, and audit events.
- Task 09's delivery statement is only partially current. Staff provisioning still uses a persistent Filament notification; Task 10C central reset/replacement uses the fragment-based delivery page and removes the fragment from browser history.
- Task 09 lists 24-hour reset expiry only implicitly through Task 10C; code confirms 48 hours for initial invitations and 24 hours for `school_admin_reset`.
- Task 09's statement that role injection was only a hypothetical risk now has a concrete Task 10B mitigation and runtime tests; it is no longer an open confirmed gap in `RolesRelationManager`.

---

## 3. Ten business decisions and recommended options

### Decision 1 — Should every student have a User account?

**Verified current behavior**

- `students.user_id` is nullable.
- Enrollment and student creation do not create a User account.
- `student_initial` invitation purpose exists and activation accepts it.
- The `student` role maps to the student panel, but `AccountProvisioningService` does not allow the `student` role.

**Business problem**

Middle and high schools may need individual student portal access, but many students have no personal email address and may share a family phone number. Automatic accounts would create unusable credentials, increase data exposure for minors, and could overload schools with activation support. Making accounts mandatory would incorrectly block enrollment.

**Realistic options**

1. No student accounts in V1.
2. Automatically create a User for every enrolled student.
3. Create an account only when the school explicitly requests it for a student or cohort and minimum identity/contact conditions are met.
4. Create accounts only when a verified dedicated student email exists.

**Security, operational, and architectural consequences**

- Option 1 is safest but delays documented digital access for schools that need it.
- Option 2 creates the largest password/invitation/support surface and risks linking shared family contacts to the wrong child.
- Option 3 keeps school records independent from portal access and allows controlled rollout by level/cohort.
- Option 4 is stricter but may exclude most target contexts where students lack personal email.

**Proposed recommendation**

Adopt option 3. Student accounts are **opt-in**, requested explicitly by the school, and limited in V1 to middle/high-school contexts where the school can confirm an appropriate access arrangement. A shared family phone number alone is not sufficient. The account remains `Pending` until activation through the existing single-use invitation. Enrollment and student records must succeed even when no account is requested.

Example French application text: **« Créer un accès élève »**, **« Cet accès sera créé uniquement à la demande de l’établissement. L’inscription scolaire reste possible sans accès informatique. »**

**RECOMMENDATION — REQUIRES OWNER APPROVAL**

---

### Decision 2 — When should a guardian receive an account invitation?

**Verified current behavior**

- No enrollment or guardian creation path issues an invitation.
- `guardian_initial` purpose exists and activation accepts it.
- The `parent` role maps to the parent panel.
- Staff invitation delivery currently uses a French Filament notification; no email/SMS provider is assumed.

**Business problem**

Not every guardian wants or needs portal access. Multiple guardians may exist, with different responsibilities. Some guardians have no email address or limited digital access. Automatically inviting every contact could disclose account existence, create duplicate invitations, and overwhelm support.

**Realistic options**

1. Invite all listed guardians during enrollment.
2. Invite after enrollment confirmation.
3. Invite only when the school explicitly requests portal access for that guardian.
4. Invite only the primary guardian.

**Security, operational, and architectural consequences**

- Options 1 and 2 improve convenience but increase accidental disclosure and support load.
- Option 4 is simple but wrong where a non-primary guardian is the actual notification or pickup contact.
- Option 3 keeps invitation issuance intentional, auditable, and aligned with existing manual delivery constraints.

**Proposed recommendation**

Adopt option 3, evaluated only after enrollment is confirmed. The request must identify the specific Guardian record, the school, and the reason for portal access. Primary and notification-recipient flags may guide the request but do not authorize it automatically. If no reliable contact channel exists, the school records the need but the system does not invent or send a link.

Example French application text: **« Inviter ce tuteur à créer un accès »**, **« Aucun e-mail ni SMS n’a été envoyé. Transmettez le lien de manière sécurisée. »**, **« Aucun canal fiable n’est disponible pour ce tuteur. »**

**RECOMMENDATION — REQUIRES OWNER APPROVAL**

---

### Decision 3 — How should the system handle an existing Guardian record with `user_id = NULL`?

**Verified current behavior**

- `guardians.user_id` is nullable.
- `CreateGuardianAction` prevents duplicate Guardian `user_id` values globally.
- No current code links a Guardian to a newly provisioned User.
- Embedded `student_guardians` rows from `GuardianReference` have `guardian_id = NULL` and cannot identify a Guardian or User.

**Business problem**

The school may already have a trustworthy Guardian record without portal access. The system must avoid creating duplicate Guardian records, but must never create or link an account merely because a name or phone number looks similar.

**Realistic options**

1. Automatically create a User whenever a Guardian has no `user_id`.
2. Link to any existing parent User with the same phone number or name.
3. Keep the Guardian record without an account and do nothing.
4. Keep the Guardian record without an account by default; when portal access is requested, resolve identity with the school-scoped matching policy and link only on exact evidence or manual verification.

**Security, operational, and architectural consequences**

- Option 1 creates accounts for people who never requested them.
- Option 2 is unsafe because phone numbers can be shared, recycled, or unverified.
- Option 3 is safe but leaves no path to portal access.
- Option 4 preserves data and gives a controlled path to access.

**Proposed recommendation**

Adopt option 4. A Guardian with `user_id = NULL` remains a school record only. Account creation or linking occurs only after an explicit portal-access request and a successful `EXACT_MATCH` or documented manual verification under section 4. No silent merge occurs, and embedded `GuardianReference` rows are never promoted to accounts on resemblance alone.

Example French application text: **« Le dossier du tuteur existe sans compte utilisateur. »**, **« Une vérification est nécessaire avant de créer ou de lier un accès. »**

**RECOMMENDATION — REQUIRES OWNER APPROVAL**

---

### Decision 4 — What happens when a matching active parent User already exists?

**Verified current behavior**

- `users.email` is globally unique, including soft-deleted rows.
- `Guardian.user_id` is nullable and can identify an existing account.
- `User` currently defines `HasOne` `guardian()` and `student()` relations, while the relationship table supports more family structures.
- No current logic reuses an existing parent account during enrollment.
- Phone matching has no verification step and normalizes only trim/format validation.
- `AccountProvisioningService` refuses to create another User with an existing email.

**Business problem**

Reusing the right account avoids duplicates and supports siblings. Reusing the wrong account could expose a child's records or merge two families. A matching phone number does not prove identity, and a cross-school email must not become a global identity link.

**Realistic options**

1. Reuse on exact email only.
2. Reuse on existing `guardians.user_id` or `student_guardians.guardian_id` inside the same school.
3. Reuse on phone number.
4. Require manual verification for all ambiguous cases.

**Security, operational, and architectural consequences**

- Email alone is strong for duplicate detection but may still be wrong if a school records a generic or reused address.
- Existing durable IDs inside the same tenant are the strongest evidence.
- Phone-only reuse is unsafe.
- Manual verification is slower but prevents account takeover and cross-tenant linking.

**Proposed recommendation**

Reuse an active parent User only when all of the following hold: the candidate is in the same `school_id`; the account has the `parent` role; and identity is established either by an existing same-school `guardians.user_id` / `guardian_id` link, or by exact normalized email plus corroborating school-scoped Guardian data. Phone number alone is only a `POSSIBLE_MATCH`. Cross-school email is `AMBIGUOUS_OR_CONFLICT` and is blocked. If the role is missing, do not silently assign it through the relation manager; any role change on an existing account must go through `UserRoleChangeService` and an explicitly approved authorization path.

Example French application text: **« Le compte existant sera réutilisé. »**, **« Correspondance possible : une vérification est nécessaire avant de lier ce compte. »**, **« Conflit : cette adresse e-mail est déjà utilisée dans un autre établissement. »**

**RECOMMENDATION — REQUIRES OWNER APPROVAL**

---

### Decision 5 — What happens when a matching User already has a Pending account and an outstanding invitation?

**Verified current behavior**

- `UserInvitation::issue()` revokes prior usable invitations for the same `user_id + school_id + purpose`.
- There is no database uniqueness on that tuple.
- Activation requires the user to remain `Pending`.
- An expired, revoked, or consumed invitation is unusable.
- No enrollment flow currently checks for an existing pending parent/student account.

**Business problem**

Several enrollment events for the same family could create duplicate pending users or invitations. Reissuing for an unrelated purpose could confuse the recipient and weaken audit clarity.

**Realistic options**

1. Reuse the existing pending account and still-usable invitation for the same purpose.
2. Revoke and replace the invitation every time.
3. Issue another invitation for a different purpose.
4. Block any further invitation when one exists.

**Security, operational, and architectural consequences**

- Reuse reduces duplicates and support work but requires robust application-level locking because the tuple lacks DB uniqueness.
- Blind rotation increases secret-handling surface.
- Different purposes may be legitimate but must be auditable and must not leave multiple active secrets.
- Blocking all reissue is too rigid when a link was never delivered.

**Proposed recommendation**

Reuse the pending account and the still-usable invitation when school, user, and purpose match. Do not issue a duplicate for the same purpose. If a different purpose is genuinely required, revoke the existing invitation and issue the new one through `UserInvitation::issue()`, with an audit event explaining the change. All checks and writes must be transactional and lock the relevant rows to compensate for the missing DB tuple constraint.

Example French application text: **« Une invitation valide existe déjà pour ce compte. »**, **« L’invitation précédente sera remplacée et ne pourra plus être utilisée. »**

**RECOMMENDATION — REQUIRES OWNER APPROVAL**

---

### Decision 6 — What happens when an invitation has expired?

**Verified current behavior**

- Initial invitations expire after 48 hours; `school_admin_reset` uses 24 hours.
- `AccountInvitationActivationService` rejects expired invitations generically.
- The User remains `Pending`; the invitation remains in history as expired but unusable.
- No reissue workflow exists for enrollment-linked invitations.

**Business problem**

Expiry is normal in schools with limited digital access. Reissuing must not create a second account or destroy audit history, and must not silently resend a secret through an unapproved channel.

**Realistic options**

1. Automatically reissue on every later enrollment event.
2. Require an authorized administrative confirmation before reissue.
3. Keep the expired invitation and create a new User.
4. Treat the account as unusable permanently.

**Security, operational, and architectural consequences**

- Automatic reissue increases operational convenience but can generate repeated secrets.
- Confirmed reissue keeps accountability.
- Creating a new User risks duplicate emails and duplicate Guardian links.
- Permanent unusability leaves a pending account that can never be activated.

**Proposed recommendation**

Keep the existing User and Guardian records. Require an authorized reissue request, then issue a new invitation with the same purpose for the same User and school. The expired record remains in audit history. Do not create a second User. Keep the 48-hour TTL; no strong reason was found to change it.

Example French application text: **« Lien expiré. Demandez une nouvelle invitation. »**, **« Une nouvelle invitation sera valable 48 heures. »**

**RECOMMENDATION — REQUIRES OWNER APPROVAL**

---

### Decision 7 — Can a student have multiple legal guardians?

**Verified current behavior**

- `student_guardians` supports multiple rows per student.
- It contains six responsibility flags, relationship type, emergency priority, pickup windows, relationship dates, notes, and append-only history.
- `LinkGuardianAction` allows multiple relationships and clears previous primary when a new primary is assigned.
- Configured relationship types include `guardian`, `legal_representative`, `foster_parent`, `sponsor`, emergency contact, and family relationships.
- `GuardianReference` rows contain contact snapshots only and cannot carry durable legal authority.

**Business problem**

A student may have several legally responsible adults, while other contacts may only be authorized for pickup, medical contact, or notifications. Treating every row as identical legal authority is wrong; treating the primary flag as the sole legal authority is also wrong.

**Realistic options**

1. Allow only one guardian per student.
2. Allow multiple contacts but use flags only as informal labels.
3. Allow multiple legal guardians, with relationship type carrying legal authority and per-student flags carrying operational responsibilities.
4. Infer legal authority from the primary flag.

**Security, operational, and architectural consequences**

- Option 1 breaks legitimate family structures.
- Option 2 weakens downstream authorization decisions.
- Option 3 matches the current schema and preserves per-child nuance.
- Option 4 overstates the meaning of an operational flag.

**Proposed recommendation**

Adopt option 3. Multiple legal guardians are permitted. Legal authority is represented by the per-tenant relationship type (`guardian`, `legal_representative`, `foster_parent`, or an approved school-specific equivalent). Responsibility flags remain per Student–Guardian relationship. `is_primary` is an operational default, not proof of exclusive legal authority. Pickup and medical grants must respect their dates and windows. Only durable rows with a non-null `guardian_id` may be used for account linking or authority-bearing portal decisions.

Example French application text: **« Plusieurs tuteurs peuvent être associés à cet élève. »**, **« Le tuteur principal est le contact par défaut ; les autres responsabilités sont définies séparément. »**

**RECOMMENDATION — REQUIRES OWNER APPROVAL**

---

### Decision 8 — Can one guardian account be linked to multiple students?

**Verified current behavior**

- `student_guardians` supports multiple rows for the same `(school_id, guardian_id)` pair with different students.
- The unique index includes `school_id`, `guardian_id`, `student_id`, and `deleted_at`, so soft-deleted rows can be recreated without blocking a new active relationship.
- `LinkGuardianAction` rejects an existing active Guardian–Student pair and enforces school ownership.
- `Guardian::relationships()` exposes multiple relationships.
- `User` currently exposes `HasOne` `guardian()` and `student()` relations, which do not model siblings by themselves.
- `PanelAccessRegistry` grants panel access by active user, mapped role, and active school; child-level data scoping is not established by that registry alone.

**Business problem**

Legitimate families have siblings in the same school. One parent account should not be duplicated per child, but portal access must not become access to every child in the tenant.

**Realistic options**

1. Create one account per child.
2. Link one User to one Guardian record, then link that Guardian to multiple Students in the same school.
3. Allow one User to link to several Guardian records in the same school.
4. Introduce a global guardian identity across schools.

**Security, operational, and architectural consequences**

- Option 1 creates duplicate accounts and shared-phone ambiguity.
- Option 2 supports siblings and preserves per-child relationship flags.
- Option 3 is useful where a person has multiple school-scoped Guardian records, but requires strict de-duplication.
- Option 4 violates current V1 tenant isolation and global email uniqueness policy.

**Proposed recommendation**

Allow option 2 as the default and option 3 only with school-scoped, verified Guardian records. A single User may be linked to one or more Guardian records inside the same school; a Guardian may be linked to multiple Students inside the same school. Never create a second User for a sibling. Before enabling guardian portal access, implementation must verify and, if necessary, harden child-level authorization so a parent role plus active school is not sufficient to read an unrelated child's records.

Example French application text: **« Ce tuteur est déjà associé à un autre élève de l’établissement. Le même compte sera réutilisé. »**, **« L’accès à chaque enfant reste limité aux relations attribuées à ce tuteur. »**

**RECOMMENDATION — REQUIRES OWNER APPROVAL**

---

### Decision 9 — How should the system resolve possible duplicate students, guardians, and accounts?

**Verified current behavior**

- Student duplicate checks use school-scoped `student_number`; `admission_number` has a DB unique index when present per school.
- Guardian duplicate checks use school-scoped `guardian_number`, `phone_number`, and `email`.
- Email value objects lowercase and trim; phone value objects only trim and validate format.
- `users.email` is globally unique including soft-deleted rows.
- No fuzzy matching, ambiguity resolution, manual review queue, or merge workflow exists.
- Embedded `GuardianReference` rows have no durable ID.

**Business problem**

The system must distinguish a true duplicate from a possible family relationship and from a conflict that requires human review. Incorrect automatic linking is a privacy and tenant-isolation failure.

**Realistic options**

1. Exact email only.
2. Normalized phone plus name.
3. A layered policy with three outcomes and manual review.
4. Always manual review.

**Security, operational, and architectural consequences**

- Exact email alone misses many school contexts and can be a shared address.
- Phone plus name is unsafe because phones are shared, recycled, and unverified.
- Layered policy is operationally practical if specific evidence rules are enforced.
- Always manual review is safest but does not scale.

**Proposed recommendation**

Adopt a layered school-scoped resolver with exactly three declared outcomes:

| Outcome | Meaning | Allowed automated action |
|---|---|---|
| `EXACT_MATCH` | Same school and a stable identifier already exists: student number/admission number for a Student; `guardian_id`, `guardian_number`, or an existing `guardians.user_id` for a Guardian; exact normalized email corroborated by same-school Guardian data for an account. | Reuse the record/account. Still recheck role, status, and ownership. |
| `POSSIBLE_MATCH` | Only weaker signals agree: normalized phone, name plus date of birth, embedded `GuardianReference`, partial email, or multiple plausible candidates. | Do **not** link. Record the reason and route to manual verification. |
| `AMBIGUOUS_OR_CONFLICT` | Multiple incompatible candidates, cross-school email, duplicate stable identifier conflict, soft-deleted email conflict, mismatched school, or contradictory ownership. | Stop automation. Record a conflict for authorized human resolution. |

When no candidate is found, the orchestrator proceeds with creation; that is an absence of a candidate, not a fourth match classification. Name alone or unverified phone alone can never produce `EXACT_MATCH`. Normalization may support comparability but is not proof of identity. No records are silently merged.

Example French application text: **« Correspondance exacte : le compte existant sera réutilisé. »**, **« Correspondance possible : une vérification est nécessaire avant de lier ce compte. »**, **« Conflit : plusieurs dossiers correspondent à ces informations. »**

**RECOMMENDATION — REQUIRES OWNER APPROVAL**

---

### Decision 10 — What should School Admins be allowed to do?

**Verified current behavior**

- School Admin provisioning is limited to `teacher`, `secretary`, and `accountant`.
- Platform provisioning is limited to `school_admin`.
- `SchoolAdminRolePolicy` and `UserRoleChangeService` are the current authoritative role controls.
- `RolesRelationManager` is now hardened through the centralized role service and cannot be used to assign arbitrary roles.
- School Admin actions must target the current tenant, and the actor/school must be active.
- Role changes are audited; rejected role changes are audited.
- No verified School Admin UI revokes invitations or suspends/deactivates User accounts.
- User and invitation records are school-scoped; activation is host-bound.

**Business problem**

Student and guardian provisioning needs new capabilities without creating a privilege-escalation path or weakening centralized role management.

**Realistic options**

1. Give School Admins full account and role management.
2. Give School Admins only narrow enrollment-linked account operations, with existing centralized role controls unchanged.
3. Move all student/guardian account operations to Super Admins.
4. Allow all actions but rely on UI filtering.

**Security, operational, and architectural consequences**

- Option 1 is unsafe because it could expose arbitrary role assignment and account suspension.
- Option 2 fits the current architecture and operational ownership.
- Option 3 is safer but impractical for school-level daily enrollment.
- Option 4 is not a server-side guarantee and contradicts Task 10B hardening.

**Proposed recommendation**

Adopt option 2. Subject to owner approval of the narrow purpose/role changes, School Admins may:

- request or trigger student/guardian account creation only through the approved enrollment orchestration;
- reuse existing accounts only under the identity matching policy;
- request invitation reissue through a dedicated audited service;
- link/unlink guardians and manage their per-student responsibility flags through existing school-scoped Guardian capabilities;
- revoke a same-school, unconsumed invitation only through a dedicated audited application service, once that capability is implemented.

School Admins must **not**:

- assign arbitrary roles;
- bypass `UserRoleChangeService` for role changes on existing accounts;
- add `student` or `parent` through the generic role relation manager;
- weaken global email uniqueness or link cross-school identities;
- suspend/deactivate accounts in V1 through a new broad capability;
- change a target school ID supplied by the browser.

If an existing account lacks the required `student` or `parent` role, the orchestrator must not silently correct it through the generic role path. That case requires an explicitly approved, audited role-change path through `UserRoleChangeService`.

Example French application text: **« Cette action est réservée à l’administrateur de l’établissement. »**, **« Le rôle ne peut pas être modifié depuis cet écran. »**, **« L’invitation a été révoquée. »**

**RECOMMENDATION — REQUIRES OWNER APPROVAL**

---

## 4. Identity matching policy

### 4.1 Status

**Proposed recommendation — not approved.** This policy operationalizes Decision 9 and constrains Decisions 3, 4, 5, 6, and 8.

### 4.2 Scope and authority

- The resolver is school-scoped. Every query must be filtered by the tenant-derived `school_id` and by an active tenant.
- The resolver returns one of three outcomes: `EXACT_MATCH`, `POSSIBLE_MATCH`, or `AMBIGUOUS_OR_CONFLICT`.
- The resolver returns evidence codes, not secrets, and never returns a plaintext token or password.
- The resolver never merges records. A separate approved action performs linking or reuse after a successful outcome.
- The resolver is used by enrollment orchestration, Guardian management, and any future administrative account-linking surface.

### 4.3 Signal strength

| Signal | Student duplicate | Guardian duplicate | Account reuse | Notes |
|---|---|---|---|---|
| Same-school `student_number` or validated `admission_number` | Strong | n/a | n/a | Stable school identifier. |
| Same-school `guardian_number`, `guardian_id`, or existing `guardians.user_id` | n/a | Strong | Strong when same-school and role-compatible | Durable identifier. |
| Exact normalized email in same school | Supporting | Strong for Guardian record | Strong only with corroboration | Email objects already lowercase/trim. |
| Exact normalized email globally | Conflict if owned by another school | Conflict if owned by another school | Conflict if another school or soft-deleted user | Global `users.email` uniqueness must remain. |
| Phone number, even normalized | Possible only | Possible only | Possible only | Not verified; may be shared or recycled. |
| Name + date of birth | Possible only | Possible only | Possible only | Useful for review, not proof. |
| Name alone | Possible only | Possible only | Possible only | Never automatic. |
| Embedded `GuardianReference` | Possible only | Possible only | Not usable for account linking | No Guardian or User ID. |
| Multiple plausible candidates | Conflict | Conflict | Conflict | Requires human resolution. |

### 4.4 Normalization rules

- Email: lowercase and trim, consistent with current provisioning and value objects.
- Phone: trim and format-validate; a future implementation may normalize digits and country prefix for comparison only. Normalization does not verify ownership.
- Names: trim Unicode spaces and case-fold for comparison only; diacritic folding is a comparison aid, not identity proof.
- Dates: exact calendar date only.
- Stable numbers: exact after trim; no fuzzy truncation.

### 4.5 Required behavior

- `EXACT_MATCH` permits automated reuse only when school ownership, account status, role compatibility, and relationship ownership are also valid.
- `POSSIBLE_MATCH` creates no automatic link. It stores a review item with the evidence codes.
- `AMBIGUOUS_OR_CONFLICT` stops automation and records a conflict.
- A soft-deleted User email is a conflict, not a reusable identity.
- A Guardian with no account is never converted into an account solely from contact resemblance.
- A cross-school email is always a conflict under current global email uniqueness.
- Manual review must be performed by an authorized school actor under a separate audited action; this report does not approve that UI yet.

---

## 5. Guardian/student relationship policy

### 5.1 Status

**Proposed recommendation — not approved.**

### 5.2 Relationship model

- The durable relationship is a row in `student_guardians` with a non-null `guardian_id`, school ID, student ID, relationship type, responsibility flags, and dates.
- Embedded `GuardianReference` rows remain contact snapshots. They preserve historical admission data and must not be silently upgraded to durable identities.
- Multiple durable rows per student are allowed.
- One durable Guardian may be linked to multiple Students in the same school.
- A soft-deleted relationship remains historical evidence; `UnlinkGuardianAction` already ends dates, clears operational flags, and soft-deletes.

### 5.3 Responsibility semantics

- `is_primary`: operational default contact; not exclusive legal authority.
- `is_emergency_contact`: emergency contact with optional priority.
- `is_authorized_pickup`: pickup permission, effective only within `pickup_valid_from` / `pickup_valid_until`.
- `is_financial_responsible`: financial responsibility for that student.
- `is_notification_recipient`: portal/communication recipient candidate; does not by itself authorize account provisioning.
- `is_medical_contact`: medical contact for that student.
- Relationship type carries legal/family category. School-specific types are tenant-owned.
- End dates, pickup windows, and history must be honored by any future authorization derived from these flags.

### 5.4 Data preservation

- `UpdateStudentAction::replaceGuardians()` currently deletes and recreates embedded rows. A future implementation must preserve or explicitly migrate durable relationships rather than treating them as disposable contact list items.
- No implementation task in this roadmap may silently merge Guardian records or overwrite non-null `guardian_id` values.
- A Guardian account may be reused for siblings, but each child relationship keeps its own flags and evidence.

---

## 6. Authorization and tenant isolation

### 6.1 Status

**Proposed recommendations — not approved.**

### 6.2 School Admin authority matrix

| Action | Proposed School Admin authority | Required guardrails |
|---|---|---|
| Provision student account | Only through approved enrollment orchestration, never a generic role form | Explicit request, eligible student, purpose `student_initial`, tenant-derived school, active actor/school |
| Provision guardian account | Only through approved enrollment/guardian orchestration | Explicit request, resolved Guardian, purpose `guardian_initial`, tenant-derived school |
| Reuse existing account | Yes, under identity policy | Same school, role compatibility, exact evidence or manual review |
| Request invitation reissue | Yes | Dedicated audited service, same school, reason, preserve history |
| Revoke invitation | Yes, after implementation | Same school, unconsumed invitation, actor active, audit reason |
| Link/unlink guardian | Yes, through existing Guardian capabilities | Same-school Student/Guardian/RelationshipType, policy authorization, history preserved |
| Manage relationship flags | Yes, through existing relationship capabilities | Per-student, date/window aware, audit |
| Assign/remove roles | Only existing approved roles through `UserRoleChangeService` | No arbitrary roles; last-admin and self-removal invariants remain |
| Suspend/deactivate User account | No in V1 | Requires separate decision and dedicated lifecycle service |
| Cross-school access | Never | Tenant from host/context, fail closed, no global guardian identity |

### 6.3 Non-negotiable controls

- Do not weaken `users.email` global uniqueness.
- Do not trust a browser-supplied `school_id`, role ID, status, password, or token.
- Do not allow School Admins to assign `super_admin`, `school_admin`, `director`, `student`, or `parent` through the generic role relation manager.
- Do not bypass `UserRoleChangeService` for role changes on existing accounts.
- Do not let a `parent` role plus active school grant access to a child without a verified `student_guardians` relationship; child-level authorization must be verified or hardened before V1 rollout.
- Do not store plaintext tokens or passwords in audit records, notifications, logs, database fields, or debug output.
- Do not treat Task 10C's central fragment-delivery flow as an email/SMS provider.

---

## 7. Architectural decisions

All items below are **proposed recommendations requiring owner approval** where they change behavior or scope.

1. **Canonical Student and Guardian namespaces**
   - Use `App\Domains\Student\Infrastructure\Models\Student`, `App\Domains\Guardian\Infrastructure\Models\Guardian`, `App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship`, and `App\Domains\Student\Infrastructure\Models\StudentGuardian` for new workflows.
   - Keep `App\Models\User` as the User aggregate used by provisioning, roles, invitations, and panel access.
   - Treat `App\Models\Student` and `App\Models\Guardian` as legacy compatibility models. Do not mix stacks inside one orchestration transaction.

2. **Canonical enrollment creation path**
   - Keep `EnrollmentService::enroll()` as the canonical entry point. Code inspection found no strong reason to replace it.
   - Keep `CreateEnrollmentAction` focused on the `Enrollment` aggregate. Account orchestration belongs outside that action.

3. **Legacy `StudentEnrollmentResource`**
   - Exclude it from V1 account provisioning. It writes `student_enrollments`, not canonical `enrollments`, and bypasses `EnrollmentService`.
   - Proposed direction: hide/deprecate it and redirect operators to `EnrollmentResource`. Final removal or rewriting requires separate owner approval because it changes an existing UI.

4. **Application-layer orchestration**
   - Add a dedicated Application-layer orchestrator, for example in the Enrollment domain, invoked by `EnrollmentService` only when account options are requested.
   - The orchestrator coordinates: Student resolution/creation, canonical Enrollment creation, Guardian resolution/linking, account resolution/provisioning, invitation issuance, audit events, and review outcomes.
   - No business orchestration in Filament pages/controllers and no implicit model observers.

5. **Reuse of `AccountProvisioningService` and `UserInvitation`**
   - Preserve them as the only User creation and token issuance mechanisms.
   - Extend role/purpose allowances only after owner approval of Decisions 1–4 and 10.
   - Do not create a second token scheme or a second User table.

6. **Safe linking of existing accounts and Guardian records**
   - Use the section 4 resolver before any link.
   - Link by durable IDs where possible; otherwise require manual verification.
   - Use `UserRoleChangeService` for any role change on an existing account.
   - Never merge Guardian records or invent `guardian_id` from an embedded contact row.

7. **Transaction boundaries and atomicity**
   - One database transaction covers the school record mutations requested in a single operation: Student/Enrollment/Guardian link/User creation/role assignment for a new account/invitation issuance.
   - Identity resolution should occur inside the same transaction or under equivalent row locking when it affects uniqueness.
   - Invitation delivery is after commit. A delivery failure does not roll back enrollment or delete the account; it produces an audited delivery-failed state.
   - Manual review outcomes are persisted outside the transaction as separate review records.

8. **Notifications and invitation delivery**
   - Introduce a delivery abstraction, but do not configure or invent an email/SMS provider in this task.
   - Preserve manual secure handover. Prefer a one-time presentation flow that does not persist the plaintext link in a Filament notification.
   - All user-facing text remains French, including expiry warnings and secure-handover instructions.

9. **Audit events and sensitive data protection**
   - Add typed events for account requested, account reused, account created, invitation issued/reissued/revoked/expired, match accepted, match needing review, conflict recorded, account activated, and delivery completed/failed.
   - Audit actor, school, target IDs, decision outcome, reason codes, and timestamps.
   - Never audit plaintext tokens, passwords, password hashes, or full secret URLs. Store only token hashes where needed.
   - Preserve existing Student, Guardian, Enrollment, role-change, and relationship history logs.

10. **Tenant isolation and school ownership validation**
    - Derive `school_id` from tenant/host context on every entry point.
    - Apply `forSchool()` to every Student, Guardian, User, relationship, enrollment, and invitation query.
    - Re-check ownership immediately before writes and inside transactions.
    - Fail closed when tenant context is absent.
    - Keep activation host-bound and cross-school invitations rejected.

---

## 8. Proposed implementation roadmap

This roadmap is a planning recommendation only. No implementation is authorized by this report.

### Task 11.1 — Identity matching and school-scoped resolution foundation

- **Objective:** Implement the section 4 resolver and review-outcome persistence.
- **Scope:** Student, Guardian, User, and embedded reference resolution; normalization helpers; evidence codes; exact/possible/conflict outcomes; manual-review record model if approved.
- **Dependencies:** Decisions 3, 4, and 9 must be approved first.
- **Expected files/areas:** New Application/Domain rules and DTOs under Student/Guardian/User domains; school-scoped repository queries; audit events; Filament review surface only after separate UI approval.
- **Main security risks:** Cross-tenant leakage, false exact match from phone/name, silent merge, evidence logging with sensitive data.
- **Acceptance criteria:** All queries fail closed without tenant; name-only and phone-only never produce `EXACT_MATCH`; cross-school email produces conflict; review outcomes preserve evidence without secrets; no record merge occurs.

### Task 11.2 — Extend controlled provisioning for student and parent purposes

- **Objective:** Allow `student` and `parent` provisioning only through explicit approved paths.
- **Scope:** Purpose/role maps in `AccountProvisioningService`; policy for allowed enrollment-linked purposes; pending-user reuse; invitation rotation for identical purpose; 48-hour TTL retained.
- **Dependencies:** Decisions 1, 2, 5, 6, and 10 must be approved first; Task 11.1 must be available for identity checks.
- **Expected files/areas:** `AccountProvisioningService`, provisioning DTOs/policies, `UserInvitation`, activation integration, targeted tests.
- **Main security risks:** Duplicate User creation, arbitrary role assignment, multiple active invitations, exposing plaintext tokens.
- **Acceptance criteria:** Existing active or soft-deleted email is never reused as a new account; same-purpose reusable invitation is reused; reissue revokes prior usable invitation; no plaintext token is persisted or audited; generic role allowlist remains unchanged unless explicitly approved.

### Task 11.3 — Guardian/User linking and relationship authorization hardening

- **Objective:** Link Users to durable Guardians safely and verify child-level access rules.
- **Scope:** `guardians.user_id` linking, `student_guardians` durable rows, sibling support, relationship flag behavior, child-level panel authorization review.
- **Dependencies:** Decisions 7, 8, and 10 must be approved first; Task 11.1 must be complete.
- **Expected files/areas:** Guardian application actions/services, relationship policies, Student/Parent panel authorization, audit listeners, history preservation.
- **Main security risks:** Parent sees another child's data; embedded rows overwrite durable relationships; duplicate Guardian records; unauthorized unlink/flag changes.
- **Acceptance criteria:** One User can serve same-school siblings without duplicate accounts; each child access is driven by an active relationship; no `guardian_id` is overwritten; unlink/flag changes are audited and historical.

### Task 11.4 — Enrollment orchestration and transaction boundaries

- **Objective:** Connect account provisioning to the canonical enrollment path without changing enrollment semantics.
- **Scope:** Application orchestrator, DTO command/result, enrollment option flags, atomicity, review outcomes, legacy resource exclusion decision.
- **Dependencies:** Tasks 11.1–11.3 must be complete; Decisions 1, 2, and 10 must be approved first.
- **Expected files/areas:** Enrollment Application layer, `EnrollmentService` façade extension, account/Guardian services, Student/Enrollment audit events, tests.
- **Main security risks:** Enrollment blocked by account failure; partial writes; bypass through legacy resource; business logic in Filament.
- **Acceptance criteria:** Enrollment without account request behaves as today; account steps are atomic; no invocation from `StudentEnrollmentResource` in V1; no observers create accounts; all tenant IDs derive from context.

### Task 11.5 — Invitation delivery and activation integration

- **Objective:** Provide a secure, French-language handover flow for student/guardian one-time links.
- **Scope:** Delivery abstraction, one-time presentation surface, expiry/reissue messaging, activation integration, no email/SMS provider assumption.
- **Dependencies:** Tasks 11.2 and 11.4 must be complete; Decisions 5 and 6 must be approved first.
- **Expected files/areas:** Presentation pages/controllers, views/DTOs, activation route integration, tests asserting no secret persistence or server-rendered exposure.
- **Main security risks:** Secret in query string, notification history, logs, database, or unintended HTTP request; browser-history persistence.
- **Acceptance criteria:** Plaintext token is not stored or audited; server-rendered HTML does not contain it; server does not receive it in a request body/query; browser fragment is removed where used; all user-facing text is French.

### Task 11.6 — Regression, multi-tenant validation, and cutover

- **Objective:** Validate the full change set and decide the legacy resource's final state.
- **Scope:** Full scenario matrix, multi-tenant tests, ambiguous-match tests, sibling tests, expiry/revocation tests, existing no-account enrollment tests, legacy bypass tests, static analysis.
- **Dependencies:** Tasks 11.1–11.5 complete.
- **Expected files/areas:** Feature/unit tests across Enrollment, Student, Guardian, User, Role, Filament resources; no `phpstan-baseline.neon` changes without separate approval.
- **Main security risks:** Regression in existing enrollment, tenant leakage, role escalation, token exposure, PHPStan/Pint drift.
- **Acceptance criteria:** Targeted suites green; full suite compared to baseline; no new baseline suppressions; legacy bypass blocked or resource formally deprecated/removed by owner decision.

---

## 9. Risks and unresolved questions

### 9.1 Main risks

1. **Child-level authorization gap:** Panel access is role/school based; verified relationship-based child scoping is not established in this phase.
2. **No automated delivery provider:** Manual handover remains a social-engineering and link-retention risk.
3. **Ambiguous identity:** Shared phones, family email addresses, and weak names can cause false positives.
4. **Global email uniqueness:** A cross-school email is a conflict, blocking legitimate reuse under current policy.
5. **Duplicate model stacks:** Mixed DDD/legacy Eloquent models can create divergent writes and tenant behavior.
6. **Embedded guardian data:** `GuardianReference`/`StudentGuardian` rows lack durable IDs and can be replaced by student updates.
7. **Legacy enrollment bypass:** `StudentEnrollmentResource` writes a different table and could circumvent orchestration.
8. **Invitation tuple uniqueness:** No DB unique constraint on `user_id + school_id + purpose`; concurrency must be handled in application logic with locks.
9. **Role-policy tension:** Student/parent provisioning needs fixed purposes, but School Admin generic role management must remain limited.
10. **Stale documentation:** Task 09's `RolesRelationManager` and central delivery statements are no longer fully current.
11. **Manual review ownership:** No existing review queue or role assignment for resolving ambiguous matches was verified.
12. **Soft-deleted users:** Emails remain reserved globally, including after soft deletion.

### 9.2 Questions requiring owner decision

- Should student accounts be limited to specific levels, cohorts, or schools, and what is the minimum age/eligibility rule?
- What counts as acceptable student contact evidence when the student has no personal email?
- Which guardian relationship types are accepted as legal authority for portal access?
- Who resolves `POSSIBLE_MATCH` and `AMBIGUOUS_OR_CONFLICT` cases, and with what evidence?
- Is a second invitation allowed if the first was never delivered, and what reason codes are required?
- Should School Admins be allowed to revoke invitations directly, or only request revoke through Super Admin support?
- Are student/parent account suspension and deactivation ever in scope, and under which separate lifecycle policy?
- Should `student` and `parent` be added to any role allowlist, or only used as fixed provisioning roles created by the orchestrator?
- Should the legacy `StudentEnrollmentResource` be hidden, redirected, or removed before V1 cutover?
- What secure channel is approved for manual link delivery in the absence of email/SMS automation?
- What proof is required before a parent account is linked to a child beyond the same school and exact contact data?

---

## 10. Explicit owner approval checklist

No Task 11 decision was approved by the original report. The Task 11.2 governance update recorded above approves Decisions 1, 2, 5, 6, and 10 only. The checkbox state for Decisions 3, 4, and 9 is intentionally left unchanged here because those decision texts were already approved and implemented in Task 11.1 and are not modified by this update.

- [x] **Decision 1:** Opt-in student accounts only with explicit school request and adequate contact/eligibility evidence. Approved by owner on 2026-11-27 for Task 11.2.
- [x] **Decision 2:** Guardian invitations only after enrollment confirmation and explicit portal-access request for the specific guardian. Approved by owner on 2026-11-27 for Task 11.2.
- [ ] **Decision 3:** Guardian without account remains unlinked unless exact same-school evidence or verified manual review succeeds; no account from name/phone resemblance. Pending owner approval.
- [ ] **Decision 4:** Active parent account reuse only with same-school durable ID or exact email plus corroboration; phone alone insufficient; cross-school email blocked. Pending owner approval.
- [x] **Decision 5:** Reuse a still-usable same-purpose invitation; revoke before different-purpose reissue; no duplicate active invitation. Approved by owner on 2026-11-27 for Task 11.2.
- [x] **Decision 6:** Expired invitation keeps User/Guardian history and requires authorized reissue with 48-hour TTL. Approved by owner on 2026-11-27 for Task 11.2.
- [ ] **Decision 7:** Multiple legal guardians allowed; relationship type carries authority; flags remain per-student and date/window aware. Pending owner approval.
- [ ] **Decision 8:** One guardian account may serve multiple same-school students via durable relationships; child-level authorization must be verified/hardened. Pending owner approval.
- [ ] **Decision 9:** Use only `EXACT_MATCH`, `POSSIBLE_MATCH`, and `AMBIGUOUS_OR_CONFLICT`, with no automatic link from name or unverified phone. Pending owner approval.
- [x] **Decision 10:** School Admins receive only narrow enrollment-linked account operations and cannot bypass `SchoolAdminRolePolicy`, `UserRoleChangeService`, or tenant isolation. Approved by owner on 2026-11-27 for Task 11.2.

### Out of scope until separately approved

- Application code, migrations, tests, config, or route changes.
- Email/SMS provider selection or configuration.
- Global cross-school identity merging.
- Weakening global email uniqueness or tenant isolation.
- Silent Guardian record merges.
- Automatic observers that create accounts.
- Generic School Admin role escalation or account suspension/deactivation.
- Modification of Task 09, Task 10A, Task 10C reports, or `phpstan-baseline.neon`.

---

## 11. Git control for this phase

### Initial status

Captured before any Task 11 work:

```text
 M app/Domains/AccountRecovery/Services/SchoolAdminPasswordResetService.php
 M app/Domains/School/DTOs/SchoolAdminReplacementResult.php
 M app/Domains/School/Services/SchoolAdminLifecycleService.php
 M app/Domains/User/DTOs/AccessRevocationCommand.php
 M app/Domains/User/Services/UserPasswordChangeService.php
 M app/Filament/Resources/SuperAdminSchoolResource/Pages/EditSuperAdminSchool.php
 M app/Http/Controllers/Auth/SchoolGatewayAuthController.php
 M docs/docs/00_Project_Management/TASK-10C_Account_Recovery_Access_Lifecycle_Report.md
 M routes/web.php
 M tests/Feature/AccountRecovery/PasswordChangeTest.php
 M tests/Feature/AccountRecovery/SchoolAdminPasswordResetTest.php
 M tests/Feature/AccountRecovery/SuperAdminLifecycleActionsTest.php
?? app/Domains/AccountRecovery/Presentation/Http/CapabilityDeliveryController.php
?? resources/views/central/capability-delivery-expired.blade.php
?? resources/views/central/capability-delivery.blade.php
```

### Final status

Captured after creating this report:

```text
 M app/Domains/AccountRecovery/Services/SchoolAdminPasswordResetService.php
 M app/Domains/School/DTOs/SchoolAdminReplacementResult.php
 M app/Domains/School/Services/SchoolAdminLifecycleService.php
 M app/Domains/User/DTOs/AccessRevocationCommand.php
 M app/Domains/User/Services/UserPasswordChangeService.php
 M app/Filament/Resources/SuperAdminSchoolResource/Pages/EditSuperAdminSchool.php
 M app/Http/Controllers/Auth/SchoolGatewayAuthController.php
 M docs/docs/00_Project_Management/TASK-10C_Account_Recovery_Access_Lifecycle_Report.md
 M routes/web.php
 M tests/Feature/AccountRecovery/PasswordChangeTest.php
 M tests/Feature/AccountRecovery/SchoolAdminPasswordResetTest.php
 M tests/Feature/AccountRecovery/SuperAdminLifecycleActionsTest.php
?? app/Domains/AccountRecovery/Presentation/Http/CapabilityDeliveryController.php
?? docs/docs/00_Project_Management/TASK-11_Student_Guardian_Account_Provisioning_Decisions.md
?? resources/views/central/capability-delivery-expired.blade.php
?? resources/views/central/capability-delivery.blade.php
```

### Explanation of changes

- The only difference from the initial status is the new untracked Task 11 report.
- All pre-existing modified and untracked Task 10C files were preserved exactly as found.
- No staging, commit, push, reset, checkout, clean, restore, or stash was performed.
- No application code, migrations, tests, configuration, routes, Task 09 report, Task 10A report, Task 10C report, or `phpstan-baseline.neon` was modified during Task 11 Phase 0.
