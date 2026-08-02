# Academic Domain — Academic Framework Engine (SPR-002)

Canonical academic framework for the multi-tenant NexusSchool platform.
Implements SPR-002 "Academic Framework Engine" on top of the FINAL SPR-001 architecture
(`ARCHITECTURE_FOUNDATION_REPORT.md`, Freeze v2.0).

The engine replaces the legacy granular academic scaffold (see
[Deprecation & transition](#deprecation--transition)) and is the single source of
truth for the academic framework. No student/teacher/finance/attendance logic lives here.

---

## 1. Vision

A school needs a stable academic skeleton before students, teachers, enrollments and
grades can exist. This engine provides it:

- **Calendar** — academic years, terms, events.
- **Structure** — education levels, programs, streams, classes, sections.
- **Catalog** — subject groups, subjects, per-class coefficients.

Everything is scoped to one school (`school_id`), multi-country ready
(BF, CI, ML, NE, TG, BJ, SN), with French-first UI labels.

## 2. Boundaries

### In scope (engine only)

- Academic years, terms, calendar events.
- Education levels, programs, streams, classes, sections.
- Subject groups, subjects, coefficients.

### Out of scope (consumers — never implemented here)

StudentEnrollment, Gradebook, Classroom, AssessmentResult, Transport, Finance,
Attendance, Scheduling, Reporting.

## 3. Aggregate & dependency graph

```
AcademicCalendarYear
├── AcademicCalendarTerm ── AcademicCalendarEvent
└── AcademicCalendarEvent

EducationLevel
├── AcademicProgram ── Stream
└── Stream

AcademicProgram ── SchoolClass ── ClassSection
                      │
                      ├── Coefficient ── AcademicSubject
                      │                       └── SubjectGroup
                      └── (Future: enrollment, timetable)
```

| From                    | To                        | Constraint                              | Delete behavior |
|-------------------------|---------------------------|-----------------------------------------|-----------------|
| AcademicCalendarTerm    | AcademicCalendarYear      | `calendar_year_id` NOT NULL             | cascade         |
| AcademicCalendarEvent   | AcademicCalendarYear      | `calendar_year_id` NOT NULL             | cascade         |
| AcademicCalendarEvent   | AcademicCalendarTerm      | `calendar_term_id` nullable             | nullOnDelete    |
| AcademicProgram         | EducationLevel            | `education_level_id` nullable           | restrict        |
| Stream                  | AcademicProgram           | `academic_program_id` NOT NULL          | cascade         |
| Stream                  | EducationLevel            | `education_level_id` nullable          | restrict        |
| SchoolClass             | AcademicProgram           | `academic_program_id` NOT NULL          | cascade         |
| SchoolClass             | EducationLevel            | `education_level_id` nullable          | restrict        |
| SchoolClass             | Stream                    | `stream_id` nullable                    | nullOnDelete    |
| ClassSection            | SchoolClass               | `school_class_id` NOT NULL              | cascade         |
| AcademicSubject         | SubjectGroup              | `subject_group_id` nullable             | nullOnDelete    |
| Coefficient             | SchoolClass               | `school_class_id` NOT NULL              | cascade         |
| Coefficient             | AcademicSubject           | `academic_subject_id` NOT NULL          | cascade         |

Deleting an entity whose children still reference it is refused (restrict) or
nulls the reference (nullOnDelete); deleting a parent with dependencies that must
not be orphaned is blocked by `EntityInUseException` at the service layer.

## 4. Domain rules (invariants)

1. **One active year per school.** DB partial unique index
   `academic_calendar_years_single_active (school_id) WHERE status = 'active'` on
   sqlite/pgsql/mariadb; MySQL falls back to service-level enforcement.
   Activating a new year demotes the current active year to `draft` (switch semantics).
2. **Terms are inside their year.** `TermOutsideCalendarYearException` otherwise.
3. **Terms do not overlap.** Touching boundaries are allowed.
   `OverlappingCalendarTermException` otherwise.
4. **Unique names per school/container** (all enforced by DB unique indexes +
   service rules):
   - year name per school; subject code and name per school;
   - stream name per program; class name per program; section name per class;
   - coefficient (class, subject) pair per school.
5. **No orphan structures.** A class always belongs to a program; a term to a year;
   a coefficient to a class+subject.
6. **Coefficient strictly positive** (`0.01..999.99`) — enforced by the
   `Coefficient` value object, not the persistence layer.
7. **Soft deletes everywhere**, `school_id` discriminator on every table for tenancy.

## 5. Structure

```
app/Domains/Academic/
├── AcademicServiceProvider.php    # repository binding, observers, policies, events
├── README.md
├── Application/
│   ├── Actions/                   # single-use use cases
│   ├── DTOs/                      # CalendarYearData, ... CoefficientData
│   └── Services/                  # CalendarService, AcademicStructureService, SubjectCatalogService
├── Domain/
│   ├── Contracts/                 # CalendarYearRepositoryInterface
│   ├── Enums/                     # CalendarYearStatus, CalendarTermStatus, CalendarEventType, EntityStatus, CountryCode
│   ├── Events/                    # CalendarYear*Created/Activated/Archived, ... Created
│   ├── Exceptions/                # ActiveCalendarYearAlreadyExists, TermOutsideCalendarYear, OverlappingCalendarTerm, DuplicateResource, EntityInUse
│   ├── Traits/                    # HandlesSchoolResourceAuthorization
│   └── ValueObjects/              # DateRange, Coefficient
├── Infrastructure/
│   ├── Listeners/                 # AcademicAuditLogger
│   ├── Models/                    # 11 Eloquent models (HasSchoolId + SoftDeletes)
│   ├── Observers/                 # AcademicEntityObserver (uuid on creating)
│   ├── Policies/                  # 11 policies (super_admin bypass + school_id + permission)
│   ├── Repositories/              # EloquentCalendarYearRepository
│   └── Rules/                     # UniqueSubjectCodePerSchool, UniqueClassNameWithinProgram, UniqueStreamWithinProgram
└── Presentation/
    └── Filament/
        ├── Resources/             # 11 resources + List/Create/Edit pages
        └── Widgets/               # CalendarOverviewWidget
```

Domain-facing files live under `database/migrations/2026_08_01_0000xx_*.php`,
`database/factories/Academic/*Factory.php`, `tests/{Unit,Feature}/Domains/Academic`.

## 6. Service layer

- `CalendarService` — create/activate/archive years (activation = switch), terms
  (validation), events, active-year queries.
- `AcademicStructureService` — levels, programs, streams, classes, sections.
- `SubjectCatalogService` — subject groups, subjects, coefficient assignment.

Services orchestrate `DB::transaction`, delegate invariant checks to Actions
(`CreateCalendarYearAction`, `ActivateCalendarYearAction`, `CreateCalendarTermAction`,
`CreateStreamAction`, `CreateSchoolClassAction`, `CreateClassSectionAction`,
`CreateSubjectAction`), and dispatch domain events.

## 7. Events & audit

| Event                         | Listener                         |
|-------------------------------|----------------------------------|
| CalendarYearCreated           | AcademicAuditLogger              |
| CalendarYearActivated         | AcademicAuditLogger              |
| CalendarYearArchived          | AcademicAuditLogger              |
| CalendarTermCreated           | AcademicAuditLogger              |
| StreamCreated                 | AcademicAuditLogger              |
| SchoolClassCreated            | AcademicAuditLogger              |
| ClassSectionCreated           | AcademicAuditLogger              |
| SubjectCreated                | AcademicAuditLogger              |

Registered in `AcademicServiceProvider` (NOT the legacy `EventServiceProvider` god-object).
Each listener writes an `audit_logs` row.

## 8. Permissions

New groups added to `App\Domains\Permission\Enums\PermissionGroup`:

`academic_calendar_year`, `academic_calendar_term`, `academic_calendar_event`,
`education_level`, `academic_program`, `stream`, `school_class`, `class_section`,
`subject_group`, `academic_subject`, `coefficient` — each with `.view/.create/.update/.delete`.

Policies: `super_admin` bypasses, otherwise `school_id` must match and the permission
must be held (`HandlesSchoolResourceAuthorization`).

## 9. Tenancy

Kept as-is (School-as-tenant, all migrations in central `database/migrations/`).
Every engine table carries `school_id` (constrained to `tenants`), every model uses
`App\Traits\HasSchoolId`, Filament resources use `App\Traits\ScopedByTenant`,
repositories scope by `forSchool($schoolId)`.

## 10. Tests

Pest. Run:

```sh
php vendor\bin\pest tests\Unit\Domains\Academic tests\Feature\Domains\Academic
```

Covers value objects, service invariants, duplicate rejection, tenancy isolation,
DB-level single-active invariant, and policy authorization.

## 11. Extension strategy

- **New consuming domain** (e.g. Gradebook): depend on this domain's `Models` +
  services; never reach into legacy scaffold.
- **Multi-country**: add `CountryCode` cases + per-country label maps; schemas are
  country-agnostic (no hardcoded class/grade counts).
- **Scheduling / timetabling**: attach to `SchoolClass`/`ClassSection` as new
  aggregate roots; reuse `DateRange` for slot validation.
- **Cross-year cloning**: extend `CalendarService` with a `cloneYear` action that
  copies calendar → terms → events, reusing existing DTOs.

## 12. Deprecation & transition

The legacy granular scaffold is **deprecated** (not deleted — other out-of-scope
domains still reference it):

- `app/Domains/{AcademicYear,AcademicTerm,Program,GradeLevel,Section,AcademicClass,Subject,SubjectCategory,SubjectCoefficient}`
- flat `app/Models/{AcademicYear,AcademicTerm,...}` + their migrations, factories, policies.

**New code must use the engine** (`App\Domains\Academic\*`). The engine is canonical.

### Transition plan

1. **Read-only freeze** — do not add features to the legacy scaffold.
2. **Data migration** — one-time backfill into engine tables (year → calendar years,
   term → calendar terms, program → programs, level → education levels, section →
   sections, class → school classes, subject → subjects, category → subject groups,
   coefficient → coefficients); map statuses to the engine enums.
3. **Cut-over** — re-point consuming domains (StudentEnrollment, Gradebook,
   Classroom, AssessmentResult) to the engine models/services; run the engine's
   invariant validation on migrated data.
4. **Purge** — remove legacy scaffold + `app/Models` mirrors + `PermissionGroup`
   legacy academic cases + legacy Filament resources, in a single dedicated PR
   after all consumers are migrated.
