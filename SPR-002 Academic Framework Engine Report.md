# SPR-002 — Academic Framework Engine · Technical Report

Status: **Implemented** · Canonical engine · Follows `ARCHITECTURE_FOUNDATION_REPORT.md` (Freeze v2.0)

---

## 1. Objective

Build the consolidated `Academic` domain ("Academic Framework Engine") for the
multi-tenant NexusSchool Laravel 12 / Filament v5.6.8 SaaS, replacing the legacy
granular academic scaffold. **Engine only**: academic years, terms, calendar events,
education levels, programs, streams, classes, sections, subject groups, subjects,
coefficients. No Student/Teacher/Finance/Attendance/Scheduling logic.

## 2. Key decisions

| # | Decision | Rationale |
|---|----------|-----------|
| D1 | New consolidated engine; legacy scaffold **deprecated**, not deleted | Legacy breaks out-of-scope domains (StudentEnrollment, Gradebook, Classroom, AssessmentResult, Transport); zero-risk path |
| D2 | Tenancy kept as-is (School-as-tenant, all migrations central, `school_id` discriminator) | No tenant DBs; engine models use `HasSchoolId`, Filament uses `ScopedByTenant` |
| D3 | Hybrid key style: auto-increment `id` + `ulid('uuid')` | Matches legacy conventions; `AcademicEntityObserver` sets `uuid` on creating |
| D4 | Per-domain `AcademicServiceProvider` owns bindings/observers/policies/events | Applies report principle D4; **does not** touch the 755-line legacy `EventServiceProvider` god-object |
| D5 | Actions + Contracts + ValueObjects introduced | Applies report principles D8 / §9–§12 |
| D6 | Single-active-year invariant: **partial unique index** `(school_id) WHERE status='active'` on sqlite/pgsql/mariadb; service enforcement on MySQL | DB-level integrity where supported; documented fallback |
| D7 | Activating a year **switches** the active year (demotes previous to draft) | Preserves legacy `AcademicYearService::activate` workflow + satisfies "one active year" |
| D8 | New permission groups in `PermissionGroup` enum | Canonical engine gets its own `*.view/create/update/delete` keys |
| D9 | `Role`/`Permission` models drop hardcoded `$connection='mysql'` (default connection) | Redundant in prod (default is already mysql); unblocks authorization tests on sqlite |

## 3. Deliverables

- **11 migrations** (`database/migrations/2026_08_01_000001..000011`), all green on SQLite `migrate:fresh`.
- **11 models** + factories, 5 enums, 2 value objects, 5 exceptions, 1 contract, 1 repository,
  4 rules, 8 events, 1 observer, 1 audit listener, 11 policies, 1 auth trait, 12 DTOs,
  8 actions, 3 services, 11 Filament resources + 33 pages, 1 widget.
- **49 Pest tests** (Unit + Feature) — all passing.
- Panel registration in `SchoolAdminPanelProvider` (navigation group **Académique**),
  plus `CalendarOverviewWidget` on the school-admin dashboard.

## 4. Schema / ER diagram

```
tenants
  ▲ 1:N (school_id)
  │
academic_calendar_years ──┬── academic_calendar_terms ──── academic_calendar_events
  (partial UNIQUE active) │        ▲ calendar_year_id (cascade)   ▲ (nullable, nullOnDelete)
                          └── academic_calendar_events ───────────┘

education_levels
  ▲ 1:N
academic_programs ──┬── streams ────────────► education_levels (nullable)
  ▲ 1:N             │  (unique per program)
school_classes ─────┘  ▲ stream_id (nullable, nullOnDelete)
  ▲ 1:N
class_sections   (unique name per class)
  ▲ 1:N
coefficients ──► academic_subjects ──► subject_groups (nullable, nullOnDelete)
  (unique class+subject per school)   (unique code per school, unique name per school)
```

Full column/constraint reference is in the 11 migration files (which are the source of truth).

## 5. Domain rules (invariants)

1. Exactly one **active** year per school (DB partial unique index + service switch).
2. Term dates strictly inside its year.
3. No overlapping terms in the same year (touching allowed).
4. Unique year name per school; unique subject code and name per school; unique
   stream/class name per program; unique section name per class; unique coefficient
   (class, subject) per school — each with DB index + service rule + French messages.
5. Class belongs to a program; term to a year; coefficient to class+subject.
6. Coefficient strictly in `0.01..999.99` (value object invariant).
7. Restrict / nullOnDelete / cascade chosen so no orphaned references and no
   destructive deletes with live dependencies (`EntityInUseException`).

## 6. Test results (this work)

| Suite | Result |
|-------|--------|
| `tests/Unit/Domains/Academic` + `tests/Feature/Domains/Academic` | **49 passed** (78 assertions) |
| Full suite | 913 passed · 198 failed — **all failures pre-existing legacy** (`Tests\Feature\Filament\*` panel tests hitting a non-existent `/admin` default panel; legacy enum label tests; `PermissionServiceTest` asserting 84 permissions). Zero regressions introduced |
| Pint | passes on all new code |
| PHPStan | **not installed** in the repo (no `phpstan` in `vendor/bin`, not in `composer.json`, config `phpstan.neon` present). Install `larastan/larastan` before the next quality gate |

> Note: `PermissionServiceTest` (`assertCount(84)`) was already failing before this
> sprint (43 enum cases × 4 ≠ 84). The 11 new groups keep it failing; it is listed in
> the transition plan for rewrite against the engine's permission model.

## 7. How the engine maps to the SPR-001 architecture

- **Per-domain provider** (D4): `AcademicServiceProvider` — repository singleton, 11 observers, 11 `Gate::policy`, 8 `Event::listen`.
- **No event god-object**: events are registered in the domain provider only.
- **Actions**: single-use-case classes validate invariants, wrap `DB::transaction`, dispatch events.
- **Contracts + repositories**: `CalendarYearRepositoryInterface` ⇄ `EloquentCalendarYearRepository` (active/overlap queries).
- **Value objects**: `DateRange` (inclusive interval, overlap with touching-safe), `Coefficient`.
- **Rules**: `UniqueSubjectCodePerSchool`, `UniqueClassNameWithinProgram`, `UniqueStreamWithinProgram`.
- **Policies**: `HandlesSchoolResourceAuthorization` (super_admin bypass + `school_id` + permission).
- **Audit**: single `AcademicAuditLogger` infra listener (per report, not 40 `Log*` listeners).
- **Dependency rule**: the domain never references another domain; legacy scaffold is not touched.

## 8. Extension strategy

- Consumers (Gradebook, Scheduling, …) depend on engine `Models`/`Services` only.
- Multi-country ready: `CountryCode` enum (BF, CI, ML, NE, TG, BJ, SN), schemas country-agnostic.
- Cross-year cloning: extend `CalendarService` (`cloneYear` copies year → terms → events).
- Timetabling: new aggregate roots on `SchoolClass`/`ClassSection`, reuse `DateRange`.

## 9. Deprecation & transition

Legacy scaffold (`app/Domains/{AcademicYear,AcademicTerm,Program,GradeLevel,Section,AcademicClass,Subject,SubjectCategory,SubjectCoefficient}`, flat `app/Models/*` mirrors, legacy academic Filament resources, legacy academic `PermissionGroup` cases) is **deprecated — read-only**. Full plan: `app/Domains/Academic/README.md` §12.

Phases: ① freeze ② backfill engine tables (status mapping to engine enums) ③ cut-over consumers ④ purge legacy in a dedicated PR.
