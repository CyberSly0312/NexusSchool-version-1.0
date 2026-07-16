# Sprint 05: Academic Year Module — Implementation Plan

> **Generated**: 2026-07-14 | **Status**: Plan Ready | **Sprint**: 05

---

## 0. Goals

Implement multi-tenant Academic Year management following DDD conventions from Sprints 01–04.

**In scope:**
- Migration, Model (updated), Enum, DTO, Rules, Service (with state transitions + clone), Policy, Events (9), Listeners (9)
- Filament Resource + Pages (list, create, edit)
- User-facing Filament relation for selecting active academic year
- One-active-year-per-school constraint
- Unit tests (23) and Feature tests (3)

**Out of scope:**
- Class/Section module (future sprint)
- Term/Period sub-divisions
- Dashboard widgets showing active year

---

## 1. Current State

| Component | Status |
|-----------|--------|
| `app/Models/AcademicYear.php` | Exists — basic model, no status enum, no description/code, no soft deletes on model |
| `database/factories/AcademicYearFactory.php` | Exists — uses hardcoded strings |
| `database/seeders/AcademicYearSeeder.php` | Exists — registered in DatabaseSeeder, creates demo year |
| `database/migrations/*_create_academic_years_table.php` | **Missing** — no migration exists |
| `app/Domains/AcademicYear/` | **Missing** — entire DDD domain layer missing |
| `app/Filament/Resources/AcademicYearResource.php` | **Missing** — no Filament resource |
| `tests/Unit/Domains/AcademicYear/` | **Missing** |
| `tests/Feature/Filament/AcademicYearResourceTest.php` | **Missing** |
| `app/Providers/EventServiceProvider.php` | Needs AcademicYear events added |

---

## 2. Database

### Migration: `create_academic_years_table`

**File**: `database/migrations/2026_07_14_000003_create_academic_years_table.php`

```php
Schema::create('academic_years', function (Blueprint $table) {
    // Identity
    $table->id();
    $table->ulid('uuid')->unique();
    $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();

    // Core data
    $table->string('name');           // "2025-2026"
    $table->string('code')->nullable(); // "AY-2025-2026"
    $table->date('start_date');
    $table->date('end_date');
    $table->text('description')->nullable();

    // Status
    $table->string('status')->default('draft'); // draft | active | archived

    // Timestamps
    $table->timestamps();
    $table->softDeletes();

    // Constraints
    $table->unique(['school_id', 'name']);
    $table->unique(['school_id', 'status'], 'academic_years_school_status_unique')
          ->where('status', 'active');  // partial: only 1 active per school
    $table->index('status');
    $table->index(['school_id', 'start_date']);
});
```

**Design decisions:**
- Partial unique index on `school_id + status` WHERE `status = 'active'` — enforces one-active-per-school at DB level
- Soft deletes for audit trail
- `code` is optional human-readable identifier
- FK `school_id` cascades on delete (if school removed, remove its years)

---

## 3. Domain Layer

### 3.1 Model Update

**File**: `app/Models/AcademicYear.php` (update existing)

Changes needed:
- Move to `app/Domains/AcademicYear/Models/AcademicYear.php` (DDD convention) OR keep in `app/Models/` since it's already there and referenced by seeder/factory — **decision: keep in `app/Models/`** for consistency with how `User` model stays in `app/Models/` while domain services live under `app/Domains/`
- Add `description` and `code` to `$fillable`
- Add `AcademicYearStatus` enum cast for `status`
- Add `scopeForSchool()` scope
- Add `isActive()`, `isDraft()`, `isArchived()` helper methods

### 3.2 Status Enum

**File**: `app/Domains/AcademicYear/Enums/AcademicYearStatus.php`

```php
enum AcademicYearStatus: string
{
    case Draft   = 'draft';
    case Active  = 'active';
    case Archived = 'archived';

    public function label(): string { ... }
    public function color(): string { ... }
}
```

### 3.3 DTO

**File**: `app/Domains/AcademicYear/DTOs/AcademicYearData.php`

```php
readonly class AcademicYearData
{
    public function __construct(
        public int $schoolId,
        public string $name,
        public string $startDate,
        public string $endDate,
        public ?string $code = null,
        public ?string $description = null,
    ) {}

    public static function fromArray(array $data): self { ... }
    public function toArray(): array { ... }
}
```

### 3.4 Rules

**File**: `app/Domains/AcademicYear/Rules/AcademicYearRules.php`

- Create rules: `name` required|string|max:100|unique per school, `start_date` required|date, `end_date` required|date|after:start_date, `school_id` required|exists:schools,id
- Update rules: same but unique ignores current record
- `validate()` static method with mode param (create/update)

### 3.5 Service

**File**: `app/Domains/AcademicYear/Services/AcademicYearService.php`

Key methods:
- `create(AcademicYearData $data): AcademicYear` — wraps in DB::transaction, dispatches `AcademicYearCreated`
- `update(AcademicYear, array $data): AcademicYear` — validates via Rules, checks if dates changed → dispatches `AcademicYearUpdated` + `AcademicYearDatesChanged` if dates modified
- `delete(AcademicYear): bool` — soft delete, dispatches `AcademicYearDeleted`
- `activate(AcademicYear): AcademicYear` — **critical**: deactivates current active year for school, activates this one. Transaction. Dispatches `AcademicYearActivated`, `AcademicYearStatusChanged`, `AcademicYearSwitched`
- `archive(AcademicYear): AcademicYear` — transitions to archived. Dispatches `AcademicYearArchived`, `AcademicYearStatusChanged`
- `clone(AcademicYear, array $overrides = []): AcademicYear` — creates new year from existing one with optional field overrides. Dispatches `AcademicYearCloned`
- `getActiveForSchool(int $schoolId): ?AcademicYear`
- `getForSchool(int $schoolId, array $filters = []): LengthAwarePaginator`
- `findById(int $id): AcademicYear`
- `hasActiveYear(int $schoolId): bool`

### 3.6 Policy

**File**: `app/Domains/AcademicYear/Policies/AcademicYearPolicy.php`

```php
viewAny(User $user): bool    → true
view(User $user, ...): bool  → true
create(User $user): bool     → $user->hasAnyRole(['super_admin', 'school_admin'])
update(User $user, ...): bool → $user->hasAnyRole(['super_admin', 'school_admin'])
delete(User $user, ...): bool → $user->hasRole('super_admin')
activate(User $user, ...): bool → $user->hasAnyRole(['super_admin', 'school_admin'])
restore(User $user, ...): bool → false
forceDelete(User $user, ...): bool → false
```

### 3.7 Events

**Files**: `app/Domains/AcademicYear/Events/`

| Event | Payload | Trigger |
|-------|---------|---------|
| `AcademicYearCreated` | `$academicYear` | Year created |
| `AcademicYearUpdated` | `$academicYear` | Year updated |
| `AcademicYearDeleted` | `$academicYear` | Year soft-deleted |
| `AcademicYearActivated` | `$academicYear` | Year status → active |
| `AcademicYearArchived` | `$academicYear` | Year status → archived |
| `AcademicYearCloned` | `$original, $clone` | Year cloned to new year |
| `AcademicYearDatesChanged` | `$academicYear, $oldStartDate, $oldEndDate` | Start/end dates modified |
| `AcademicYearSwitched` | `$school, $previousYear, $newYear` | Active year changed for school |
| `AcademicYearStatusChanged` | `$academicYear, $oldStatus, $newStatus` | Any status transition |

All follow the same pattern as `RoleCreated`: `Dispatchable`, `SerializesModels`, readonly properties.

### 3.8 Listeners

**Files**: `app/Domains/AcademicYear/Listeners/`

| Listener | Event | Action |
|----------|-------|--------|
| `LogAcademicYearCreated` | `AcademicYearCreated` | `Log::info(...)` |
| `LogAcademicYearUpdated` | `AcademicYearUpdated` | `Log::info(...)` |
| `LogAcademicYearDeleted` | `AcademicYearDeleted` | `Log::info(...)` |
| `LogAcademicYearActivated` | `AcademicYearActivated` | `Log::info(...)` |
| `LogAcademicYearArchived` | `AcademicYearArchived` | `Log::info(...)` |
| `LogAcademicYearCloned` | `AcademicYearCloned` | `Log::info(...)` |
| `LogAcademicYearDatesChanged` | `AcademicYearDatesChanged` | `Log::info(...)` |
| `LogAcademicYearSwitched` | `AcademicYearSwitched` | `Log::info(...)` |
| `LogAcademicYearStatusChanged` | `AcademicYearStatusChanged` | `Log::info(...)` |

### 3.9 EventServiceProvider Update

**File**: `app/Providers/EventServiceProvider.php`

Add all 9 events → listeners.

---

## 4. Filament Resource

### 4.1 Resource

**File**: `app/Filament/Resources/AcademicYearResource.php`

- `$model = AcademicYear::class`
- `$navigationIcon = 'heroicon-o-calendar-days'`
- `$navigationGroup = 'Academics'`
- `$navigationSort = 10`
- `$recordTitleAttribute = 'name'`

**Form:**
```
Section('Academic Year Details')
  ├── TextInput('name') — required, max 100
  ├── TextInput('code') — optional, max 50
  ├── DatePicker('start_date') — required
  ├── DatePicker('end_date') — required, after start_date
  └── Textarea('description') — optional

Section('Status')
  └── Select('status') — options from AcademicYearStatus enum, default 'draft'
```

**Table:**
```
Columns:
  ├── TextColumn('name') — searchable, sortable
  ├── TextColumn('code') — toggleable
  ├── TextColumn('start_date') — date format, sortable
  ├── TextColumn('end_date') — date format, sortable
  ├── TextColumn('status') — badge from enum color()
  └── TextColumn('created_at') — dateTime, toggleable

Actions:
  ├── EditAction
  ├── ActivateAction (custom — visible only when status !== 'active')
  │     → calls AcademicYearService::activate()
  └── DeleteAction

BulkActions:
  └── DeleteBulkAction

Filters:
  └── SelectFilter('status') — AcademicYearStatus options
```

### 4.2 Pages

**File**: `app/Filament/Resources/AcademicYearResource/Pages/ListAcademicYears.php`
- Header actions: `CreateAction`

**File**: `app/Filament/Resources/AcademicYearResource/Pages/CreateAcademicYear.php`
- Standard CreateRecord

**File**: `app/Filament/Resources/AcademicYearResource/Pages/EditAcademicYear.php`
- Header actions: `DeleteAction`

---

## 5. Tests

### 5.1 Unit Tests

**File**: `tests/Unit/Domains/AcademicYear/AcademicYearServiceTest.php`

| # | Test |
|---|------|
| 1 | `test_can_create_academic_year` |
| 2 | `test_can_update_academic_year` |
| 3 | `test_can_delete_academic_year` |
| 4 | `test_can_activate_academic_year` |
| 5 | `test_activating_new_year_deactivates_previous` |
| 6 | `test_can_archive_academic_year` |
| 7 | `test_cannot_activate_already_active_year` |
| 8 | `test_cannot_archive_non_active_year` |
| 9 | `test_can_get_active_for_school` |
| 10 | `test_returns_null_when_no_active_year` |
| 11 | `test_can_check_has_active_year` |
| 12 | `test_can_paginate_academic_years` |
| 13 | `test_can_filter_by_status` |
| 14 | `test_can_search_by_name` |
| 15 | `test_can_clone_academic_year` |
| 16 | `test_clone_preserves_source_fields` |
| 17 | `test_clone_allows_overrides` |
| 18 | `test_events_dispatched_on_create` |
| 19 | `test_events_dispatched_on_activate` |
| 20 | `test_status_changed_event_dispatched_on_activate` |
| 21 | `test_switched_event_dispatched_on_activate` |
| 22 | `test_dates_changed_event_dispatched_on_date_update` |
| 23 | `test_cloned_event_dispatched_on_clone` |

### 5.2 Feature Tests

**File**: `tests/Feature/Filament/AcademicYearResourceTest.php`

| # | Test |
|---|------|
| 1 | `test_can_render_academic_year_list_page` |
| 2 | `test_can_render_create_academic_year_page` |
| 3 | `test_can_render_edit_academic_year_page` |

---

## 6. File Manifest

### New files (23)
```
app/Domains/AcademicYear/
├── Enums/AcademicYearStatus.php
├── DTOs/AcademicYearData.php
├── Rules/AcademicYearRules.php
├── Services/AcademicYearService.php
├── Policies/AcademicYearPolicy.php
├── Events/
│   ├── AcademicYearCreated.php
│   ├── AcademicYearUpdated.php
│   ├── AcademicYearDeleted.php
│   ├── AcademicYearActivated.php
│   ├── AcademicYearArchived.php
│   ├── AcademicYearCloned.php
│   ├── AcademicYearDatesChanged.php
│   ├── AcademicYearSwitched.php
│   └── AcademicYearStatusChanged.php
└── Listeners/
    ├── LogAcademicYearCreated.php
    ├── LogAcademicYearUpdated.php
    ├── LogAcademicYearDeleted.php
    ├── LogAcademicYearActivated.php
    ├── LogAcademicYearArchived.php
    ├── LogAcademicYearCloned.php
    ├── LogAcademicYearDatesChanged.php
    ├── LogAcademicYearSwitched.php
    └── LogAcademicYearStatusChanged.php

app/Filament/Resources/
├── AcademicYearResource.php
└── AcademicYearResource/Pages/
    ├── ListAcademicYears.php
    ├── CreateAcademicYear.php
    └── EditAcademicYear.php

database/migrations/2026_07_14_000003_create_academic_years_table.php

tests/Unit/Domains/AcademicYear/AcademicYearServiceTest.php
tests/Feature/Filament/AcademicYearResourceTest.php
```

### Modified files (3)
```
app/Models/AcademicYear.php          — add casts, fillables, helpers
database/factories/AcademicYearFactory.php — use enum, add code/description
app/Providers/EventServiceProvider.php     — add 9 event mappings
```

---

## 7. Verification

After implementation:
1. `php artisan migrate` — verify migration runs
2. `./vendor/bin/pint` — verify code style
3. `./vendor/bin/phpunit tests/Unit/Domains/AcademicYear/` — unit tests pass
4. `./vendor/bin/phpunit tests/Feature/Filament/AcademicYearResourceTest.php` — feature tests pass
5. `./vendor/bin/phpunit` — full suite passes (53+ tests)
6. `php artisan tinker` — verify model works, status enum casts correctly

---

## 8. Execution Order

1. Migration → Model update → Enum
2. DTO → Rules → Service
3. Policy
4. Events (9) → Listeners (9) → EventServiceProvider update
5. Factory update
6. Filament Resource + Pages
7. Unit tests
8. Feature tests
9. Pint + full test suite
10. Plan exit review
