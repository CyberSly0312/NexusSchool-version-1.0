# SPR-003 — Student Domain · Technical Report

Status: **Implemented** · Canonical aggregate root · Follows `ARCHITECTURE_FOUNDATION_REPORT.md` (Freeze v2.0)

---

## 1. Objective

Build the consolidated `Student` domain for the multi-tenant NexusSchool
Laravel 12 / Filament v5.6.8 SaaS, replacing the legacy granular student
scaffold with a canonical engine that mirrors the SPR-002 Academic Framework:
enums, value objects, exceptions, events, rules, DTOs, actions, services,
infrastructure (models/observer/policy/audit), Filament presentation, and a
Pest test suite. The domain owns the **full student lifecycle**: create,
update, transfer, suspend, graduate, withdraw, reactivate, archive (soft-delete),
restore.

## 2. Key decisions

| # | Decision | Rationale |
|---|----------|-----------|
| D1 | Canonical domain in `app/Domains/Student/{Application,Domain,Infrastructure,Presentation}`; legacy scaffold **deprecated, not deleted** | Legacy dais (StudentEnrollment, Gradebook, Transport, Library, `DocumentGenerationService`) stays untouched — same zero-risk path as SPR-002 D1 |
| D2 | **No BC alias on `App\Models\Student`** | An initial `extends CanonicalStudent` alias changed legacy `status` casts (canonical vs legacy enum) and factory resolution, breaking 57 legacy tests vs baseline; reverted to the untouched legacy model — consistent with SPR-002 D1 (legacy models in `app/Models/` coexist with canonical models) |
| D3 | Tenancy kept as-is: `HasSchoolId` on models, `ScopedByTenant` on Filament, `school_id` string discriminator | Same as SPR-002 D2 |
| D4 | Hybrid key style: auto-increment `id` + `ulid('uuid')` via `StudentEntityObserver` | Matches legacy + Academic conventions |
| D5 | Per-domain `StudentServiceProvider` owns bindings/observers/policy/events; legacy `EventServiceProvider` untouched | Applies report principle D4 |
| D6 | Actions enforce invariants inside `DB::transaction`; `StudentService` is a thin facade over the actions | Applies report principles D8 / §9–§12 |
| D7 | Lifecycle statuses (Active/Inactive/Suspended/Withdrawn/Graduated/Archived) with **immutable terminal states** (Graduated, Archived); invalid transitions throw `InvalidStudentStateException` | State machine enforced in actions, never in the model |
| D8 | **Archived == soft-delete**: `ArchiveStudentAction` sets `status=Archived` + `deleted_at`; `archived()` factory state mirrors it; `TrashedFilter` + `restore` action reverse it | Single source of truth for "archived"; default scope excludes trashed |
| D9 | `student_number` and `email` unique **per school** (DB `unique(['school_id', …])` + service checks); generated `STU-{PREFIX}-{SEQ}` with a 6-char hash prefix of the school id | Raw ULID prefixes collide between schools (`TEST-0`); stable hashed prefix + count-based (soft-delete-safe) sequence |
| D10 | New permission group `Student` (`student.*`) + `EXTRA_ACTIONS['student']` (transfer/suspend/graduate/archive/restore) in `PermissionService`; lifecycle actions visible in Filament only when permitted | Matches SPR-002 D8/D9 permission model |

## 3. Deliverables

- **2 migrations** (`database/migrations/2026_08_01_000012..000013`), green on SQLite `migrate:fresh`.
- **54 canonical files**: 2 models (+1 canonical factory, 1 seeder), 3 enums, 11 value objects,
  3 exceptions, 9 events, 2 rules, 1 auth trait, 3 DTOs, 9 actions, 2 services,
  1 observer, 1 policy, 1 audit listener, 1 provider, 1 Filament resource + 3 pages + 1 relation manager.
- **100 Pest tests passing** (Unit + Feature + Arch).
- Panel registration in `SchoolAdminPanelProvider` (navigation group **Élèves**), replacing the
  legacy `\App\Filament\Resources\StudentResource` registration.

## 4. Schema / ER diagram

```
tenants
  ▲ 1:N (school_id)
  │
students
  ├── academic_calendar_years  (academic_calendar_year_id, nullable, nullOnDelete)
  ├── school_classes           (school_class_id, nullable, nullOnDelete)
  ├── class_sections           (class_section_id, nullable, nullOnDelete)
  └── 1:N ── student_guardians (student_id, cascade; school_id; is_primary)
```

New columns on `students` (all nullable/defaulted, so legacy inserts stay valid):
`birth_certificate_reference`, structured `address_*`, `emergency_contact_*`,
`academic_calendar_year_id`, `school_class_id`, `class_section_id`,
`transfer_status` (default `none`) + transfer timestamps, lifecycle timestamps
(`graduated_at`, `suspended_at`, `suspended_until`, `withdrawn_at`,
`reactivated_at`, `archived_at`) + reasons. Uniques: `(school_id, student_number)`,
`(school_id, email)`. Full column reference is in the migration files (source of truth).

## 5. Domain rules (invariants)

1. `student_number` unique per school; `email` optional but unique per school when present.
2. `BirthDate` must not be in the future (value object).
3. `admission_date` must fall inside the assigned **active** academic year
   (`CarbonImmutable` range check in `CreateStudentAction`).
4. The class must belong to the school and the section must belong to the class.
5. Lifecycle transitions validated in actions: no double graduate; archive only from
   non-archived states; restore only from archived; immutable states reject updates/transfers.
6. Guardians: first non-empty guardian row is primary; empty rows are dropped
   (`GuardianReference::fromNullableGuardian`); validated via `PhoneNumber`/`EmailAddress` VOs.
7. Archived == `status=Archived` **and** `deleted_at` set (D8); soft-delete only (no hard deletes).
8. `PhoneNumber`, `EmailAddress`, `GuardianReference` VOs validate format/relationship;
   names must be non-empty.

## 6. Test results (this work)

| Suite | Result |
|-------|--------|
| `tests/Unit/Domains/Student` + `tests/Feature/Domains/Student` + `tests/Arch/StudentDomainArchitectureTest.php` | **100 passed** (176 assertions) |
| Legacy `tests/Unit/Domains/Student/StudentServiceTest.php` + `tests/Unit/Domains/StudentEnrollment/StudentEnrollmentServiceTest.php` | **57 passed** — restored to baseline (see D2) |
| Full suite | 982 passed · 198 failed — **identical to the pre-sprint baseline** (pre-existing legacy Filament panel tests, stale enum label tests, `PermissionServiceTest`). Zero regressions introduced |
| Pint | passes on all new code |
| PHPStan (level 5) | no `larastan` installed; remaining 74 errors on the new paths are the codebase-wide Eloquent-magic class (`property.notFound`, `Builder::forSchool`, `auth()->user()`, `Model::create`) — the SPR-002 Academic domain carries the same class (154 errors). Genuine bugs found by the run were fixed: missing `InvalidArgumentException` import in `Nationality`, wrong `: Closure` return type in `RuleUnique` (was a runtime `TypeError`), non-existent `Filament\Forms\Get` type, docblock/param mismatches, and an untyped `$record` in `EditStudent` |

## 7. How the domain maps to the SPR-001 architecture

- **Per-domain provider** (D4): `StudentServiceProvider` — `singleton`s for the service/generator,
   `StudentEntityObserver`, `Gate::policy(Student::class, StudentPolicy::class)`, event listeners → `StudentAuditLogger`.
- **No event god-object**: lifecycle events are registered in the domain provider only.
- **Actions**: single-use-case classes (`CreateStudentAction`…`RestoreStudentAction`) validate
  invariants, wrap `DB::transaction`, dispatch domain events with `?User $actor`.
- **Services**: `StudentService` is the facade over the actions (`register/update/transfer/…/restore`
  + `paginate`); `StudentNumberGenerator` produces the school-scoped sequence.
- **Value objects**: `StudentName`, `BirthDate`, `Nationality`, `PhoneNumber`, `EmailAddress`,
  `StudentNumber`, `AdmissionReference`, `Photo`, `Address`, `EmergencyContact`, `GuardianReference`.
- **Rules**: `StudentRules` (create/update vocabulary) + `RuleUnique` (school-scoped `unique` rules).
- **Policies**: `HandlesStudentAuthorization` (super_admin bypass + `school_id` + permission, lifecycle perms).
- **Audit**: single `StudentAuditLogger` infra listener writing `App\Models\AuditLog`.
- **Dependency rule**: the domain never references another domain except the SPR-002 `Academic`
  models for assignment validation; legacy scaffold is not touched.

## 8. Extension strategy

- Consumers (StudentEnrollment, Gradebook, …) depend on the legacy `App\Models\Student` until a
  cut-over PR points them at the canonical `App\Domains\Student\Infrastructure\Models\Student`.
- Guardians are modelled as `GuardianReference` value objects today; promoting them to first-class
  aggregates later is additive (`student_guardians` already exists).
- Attendance / discipline / assessments attach to the canonical `Student` aggregate root.
- Transfer workflow can grow `StudentTransferred`→ school-change orchestration on the existing
  `transfer_status`/`transfer_from_school`/`transfer_to_school` columns.

## 9. Deprecation & transition

Legacy scaffold (`app/Domains/Student/{Services,DTOs,Enums,Events,Listeners,Policies,Rules}`, flat
`app/Models/Student.php`, legacy `app/Filament/Resources/StudentResource*`) is **deprecated —
read-only**. Same phasing as SPR-002 §9: ① freeze ② backfill canonical columns ③ cut-over consumers
to the canonical model/actions ④ purge legacy in a dedicated PR.
