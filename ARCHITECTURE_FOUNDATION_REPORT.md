# NEXUS SCHOOL — Foundation Architecture Report

**Sprint:** SPR-001.1 — Foundation Architecture
**Status:** DEFINITIVE — Architecture Freeze v2.0 (supersedes v1.0)
**Date:** 2026-08-01
**Author:** Principal Software Architect
**Scope:** Architecture only. No business modules implemented. No production code.

> This document is the single source of truth for the NexusSchool architecture. Every future
> module MUST conform to it. Every architectural decision recorded here is normative.
> Contradictory documents (`ARCHITECTURE_REPORT.md`, `CGPT_NexusSchool_Architectural_Audit.md`,
> `Architecture & Product Review v0.9.md`) are **superseded** by this report for structural matters.

---

## Table of Contents

1. [Current Architecture Review](#1-current-architecture-review)
2. [Problems Found](#2-problems-found)
3. [Recommended Improvements](#3-recommended-improvements)
4. [Final Folder Structure](#4-final-folder-structure)
5. [DDD Diagram (text)](#5-ddd-diagram-text)
6. [Dependency Rules](#6-dependency-rules)
7. [Domain Boundaries](#7-domain-boundaries)
8. [Naming Conventions](#8-naming-conventions)
9. [Service Layer Strategy](#9-service-layer-strategy)
10. [Repository Strategy](#10-repository-strategy)
11. [DTO Strategy](#11-dto-strategy)
12. [Action Pattern](#12-action-pattern)
13. [Event Strategy](#13-event-strategy)
14. [Policy Strategy](#14-policy-strategy)
15. [Infrastructure Overview](#15-infrastructure-overview)
16. [Shared Components](#16-shared-components)
17. [Future Module Integration Strategy](#17-future-module-integration-strategy)
18. [Migration Strategy](#18-migration-strategy)
19. [Risks](#19-risks)
20. [Recommendations](#20-recommendations)

**Annexes**
- [A. Panel Architecture](#annex-a-panel-architecture)
- [B. Multi-Tenancy Boundary Specification](#annex-b-multi-tenancy-boundary-specification)
- [C. Security Review](#annex-c-security-review)
- [D. Definition of Done — Validation](#annex-d-definition-of-done-validation)

---

## 1. Current Architecture Review

### 1.1 Inventory (verified against the repository, 2026-08-01)

| Component | Value | Assessment |
|---|---|---|
| PHP runtime | `8.2.12` (ZTS) | ❌ Below target `^8.4` |
| Laravel | `v12.63.0` | ✅ |
| Filament | `v5.6.8` (composer `^5.6`) | ⚠️ Task spec says Filament v4; repo already on v5 |
| Livewire | `v4.3.3` | ✅ |
| Stancl Tenancy | `v3.10.0` | ✅ |
| Spatie Permission | `6.25.0` | ✅ |
| Sanctum | `v4.3.2` | ✅ |
| Pest | **not installed** | ❌ PHPUnit `11.5` only |
| Central migrations | `98` files in `database/migrations/` | ❌ ALL business tables are central |
| Tenant migrations | `0` files in `database/migrations/tenant/` | ❌ **CRITICAL** |
| Models | `84` in `app/Models/` (flat, shared) | ❌ Not domain-owned |
| Domain folders | `34` under `app/Domains/` | ⚠️ Inconsistent maturity |
| Filament Resources | `66` in `app/Filament/Resources/` (flat, shared) | ❌ Shared namespace, panels duplicate |
| Panels | `8` providers (`SuperAdmin`, `SchoolAdmin`, `Director`, `Secretary`, `Accountant`, `Teacher`, `Student`, `Parent`) | ⚠️ Duplicated middleware, no base |
| Event registrations | `~200` mappings hand-wired in one 755-line `EventServiceProvider` | ❌ God-object |
| Tests | `~220` PHPUnit tests in `tests/Unit` + `tests/Feature` | ⚠️ No Pest, no tenancy suite |
| Route files | `web.php`, `api.php`, `console.php`, `tenant.php` (empty) | ⚠️ No central/tenant split |
| Policies | 1 central (`UserPolicy`) + per-domain policies in `app/Domains/*/Policies` | ✅ pattern exists |
| DTOs | `readonly` classes, `fromArray()/toArray()`, per-domain `DTOs/` | ✅ solid |
| Services | per-domain `Services/`, transaction-wrapped, dispatch events | ✅ solid |
| Actions / Repositories / Contracts / ValueObjects | **absent** | ❌ |

### 1.2 What already works well

- **Domain-first thinking** is real: `app/Domains/*` exists for 34 bounded contexts, each with
  `DTOs`, `Enums`, `Events`, `Listeners`, `Policies`, `Rules`, `Services`.
- **DTO discipline**: immutable `readonly` DTOs with named-argument constructors.
- **Services orchestrate transactions and dispatch domain events** (`DB::transaction` + `Event::dispatch`).
- **Per-domain policies** with permission gates already in place.
- **Panels are already role-scoped** via `User::canAccessPanel()` (role → panel mapping).
- **Tenancy is present** with bootstrappers for database, cache, filesystem, queue.
- **Governance** is strong: ADR culture, freeze v1.0, comprehensive docs.

### 1.3 What is structurally broken

1. **Tenancy is not actually enforced.** `tenant_model => School::class` + `0` tenant migrations.
   Every business table is created in the **central** database; the tenant database created by
   Stancl contains **no tables**. Either isolation silently never happens, or the app runs
   everything on central. Both are unacceptable.
2. **Models live outside domains** (`app/Models`, namespace `App\Models`), while their DTOs,
   policies and events live in domains. The bounded context is physically split in half.
3. **School == Tenant**, which previous audits correctly identified as wrong for the multi-campus
   product vision (a customer = organization with multiple schools).
4. **The event wiring is a god-object** and contains duplicate registrations
   (e.g., `SubjectCreated`, `SubjectUpdated`, `SubjectArchived` are each mapped twice).
5. **Filament resources are a flat shared namespace.** Resources owned by the central SaaS
   context (`PlanResource`, `SubscriptionResource`, `CentralUserResource`, `DomainResource`,
   `SuperAdminSchoolResource`) sit beside tenant resources. Panels "borrow" resources
   ("hidden, for future panels") — duplicated responsibilities.
6. **No Actions, no Repositories, no Contracts, no Value Objects, no per-domain providers.**
7. **Testing framework gap:** Pest absent; no tenant-database test suite; no panel-isolation tests.
8. **PHP < 8.4 and Filament mismatch with the stated stack.**

---

## 2. Problems Found

Ranked by severity. Each maps to an architectural decision in §3.

### P1 — CRITICAL: Tenant isolation is nominal only
- `database/migrations/tenant/` is empty (0 files).
- `config/tenancy.php` sets `tenant_model => School::class` and enables
  `DatabaseTenancyBootstrapper` → a tenant database is created and the connection switched, but
  no schema exists in it.
- All 98 migrations target the central database.
- **Consequence:** either all tenants share one schema (data leakage) or the tenant DB is empty
  (app crashes). There is no in-between. This invalidates the core SaaS promise.

### P2 — CRITICAL: Domain cohesion is broken by `app/Models`
- 84 models in `App\Models`; domains in `App\Domains\*` reference `App\Models\*`.
- The domain boundary is physical, not logical: a developer can modify a model without touching
  its domain, and there is no compiler/linter enforcing domain ownership.
- The tenant model lives in `App\Domains\School\Models\School` (the *only* domain-owned model),
  proving the intended location for models is the domain.

### P3 — HIGH: School-as-tenant blocks the product vision
- One DB per school ⇒ a school group with N campuses gets N disconnected databases.
- Teachers, students and staff cannot span schools; finance/reporting is fragmented.
- The correct tenant is the **Organization**; `school_id` (now `campus_id` semantics) becomes an
  explicit discriminator **inside** the tenant database.

### P4 — HIGH: Event registration is a god-object and duplicates listeners
- `EventServiceProvider::listen` (755 lines) manually imports ~200 classes.
- Duplicated mappings exist today (`SubjectArchived` ×2, `SubjectCreated` ×2, `SubjectUpdated` ×2).
- Adding a domain event requires editing a central file → every domain is coupled to the center.
- Listeners named `Log*` imply the audit is event-driven, but audit *should* be an
  infrastructure concern, not hand-wired per event.

### P5 — HIGH: Panel architecture risks resource leakage
- Flat `App\Filament\Resources` namespace shared by 8 panels.
- `SchoolAdminPanelProvider` registers `AssessmentResultResource`, `IncidentResource`,
  `MessageResource`, `PaymentResource`, etc., "masked for future panels" — responsibilities
  duplicated across panels, no single owner.
- Panel providers copy-paste the entire middleware stack (8 panels × 11 middlewares).
- No central/tenant panel separation (SuperAdmin is central; the rest are tenant).

### P6 — HIGH: No tenant context security at panel level
- Tenant initialization for tenant panels relies on
  `InitializeTenancyForPanel` reading `Session::get('tenant_id')` and calling
  `Tenancy::initialize($school)` **before authentication is guaranteed**.
- It does not verify the authenticated user *belongs to* that tenant, does not re-verify the
  tenant on each request (no tenant-bound session), and does not use Stancl's own
  `InitializeTenancyByDomain` middleware.
- `canAccessPanel()` returns `true` for any `super_admin` on **any** panel — including tenant
  panels — without a tenant context.

### P7 — HIGH: No domain autonomy
- No per-domain `ServiceProvider`. All domain binding and wiring is centralized in
  `AppServiceProvider` (ULID generation) + `EventServiceProvider`.
- Domains cannot be enabled/disabled, tested in isolation, or understood without reading central files.

### P8 — MEDIUM: Missing architectural building blocks
- No `Actions/`, `Contracts/`, `Repositories/`, `ValueObjects/` folders.
- Services sometimes mix query logic (`paginate()`, `findById()`) with business workflows —
  acceptable for now, but must be codified to avoid "service that is only a query layer".

### P9 — MEDIUM: Inconsistent ID strategy
- `string('id')` (Stancl tenants), `uuid('id')` (some models), `id()` + `ulid('uuid')` (others),
  plus a global `Model::creating` hook in `AppServiceProvider` AND per-model `booted()` ULID
  generators. Three sources of truth for the same concern.

### P10 — MEDIUM: Stack drift
- PHP `^8.2` in `composer.json` (runtime 8.2.12) vs required PHP 8.4+.
- Filament v5 installed while the task stack says v4.
- Pest absent while the stack says PHPUnit/Pest.

### P11 — MEDIUM: No tenant test infrastructure
- `phpunit.xml` uses in-memory SQLite on a single connection. No test for: tenant DB creation,
  tenant data isolation, cross-tenant leakage, panel role isolation.

### P12 — LOW: Routing does not separate central/tenant
- `routes/tenant.php` is empty; `web.php` holds nothing but a welcome route. Panels register
  their own routes. No explicit central boundary in routing.

---

## 3. Recommended Improvements

Each problem → decision. These are normative.

| # | Decision | Fixes |
|---|---|---|
| D1 | **Tenant = Organization.** `Organization` extends `Tenant`; `School`/`Campus` become ordinary tenant models with `organization_id` implicit (tenant DB) and `school_id` explicit. | P1, P3 |
| D2 | **Full tenant migration split.** `database/migrations/` = central only; `database/migrations/tenant/` = all business tables. | P1 |
| D3 | **Domain-owned models.** Every Eloquent model moves into its owning domain's `Infrastructure/Models/`. `App\Models` is retired. | P2 |
| D4 | **Per-domain ServiceProvider.** Each domain boots its own events, bindings, policies, observers, and Filament resources. | P4, P7 |
| D5 | **Kill the event god-object.** `EventServiceProvider` is emptied to framework minimum; per-domain providers own registrations. Audit becomes an infrastructure listener, not 40 `Log*` listeners. | P4 |
| D6 | **Panel-scoped presentation.** Resources are owned by domains (`app/Domains/{Domain}/Presentation/Filament/Resources`); panels explicitly register only their own list; automatic discovery is disabled; a shared `BasePanel` supplies the common middleware stack. | P5 |
| D7 | **Tenant-bound auth + middleware.** Tenant panels use a hardened `EnsureTenantContext` middleware that binds the tenant at login, verifies user→tenant membership, and forbids runtime tenant switching. | P6 |
| D8 | **Introduce Actions, Contracts, Value Objects.** Codify the missing building blocks (§9–§12). | P8 |
| D9 | **Uniform ULID primary keys.** All PKs become ULID strings. Remove the global `Model::creating` hook and per-model `booted()` generators in favor of a single base trait. | P9 |
| D10 | **Bump the stack.** PHP `^8.4`, keep Filament v5 (document deviation from v4 spec), add Pest. | P10 |
| D11 | **Tenant test suite + Pest.** Add `tests/Tenancy`, panel isolation tests, Pest as primary test layer. | P11 |
| D12 | **Route split.** `routes/central.php` (super-admin, SaaS webhooks, central API) vs `routes/tenant.php` (tenant panels, tenant API). | P12 |

**Sequencing:** D1–D3 are foundational (data + ownership). D4–D7 depend on D3 (domain providers
need domain models; panels need domain resources). D8–D12 are parallelizable after D1.

---

## 4. Final Folder Structure

### 4.1 Top level

```text
NexusSchool/
├── app/
│   ├── Core/                    # Shared Kernel + platform-wide infrastructure (no business logic)
│   ├── Domains/                 # Bounded contexts — the business
│   ├── Infrastructure/          # Cross-cutting technical infrastructure (Pdf, Filesystem, Queue…)
│   └── Panel/                   # Filament panels (presentation root, thin)
├── bootstrap/
├── config/
├── database/
│   ├── migrations/              # CENTRAL ONLY
│   ├── migrations/tenant/       # TENANT ONLY (all business tables)
│   ├── factories/               # split later: central/ + tenant/
│   └── seeders/                 # split later: central/ + tenant/
├── docs/
├── lang/
├── public/
├── resources/
├── routes/
│   ├── central.php              # central web (super-admin panel, SaaS endpoints)
│   ├── tenant.php               # tenant web (tenant panels)
│   ├── api/                     # v1 tenant API + central API
│   └── console.php
├── storage/
├── tests/
│   ├── Unit/
│   ├── Feature/
│   ├── Tenancy/                 # tenant isolation + lifecycle tests
│   └── Pest.php
├── composer.json
├── pint.json
├── phpstan.neon
└── phpunit.xml
```

**Decision rationale:** the current `app/Filament` is renamed to `app/Panel` to remove the
implication that presentation *is* Filament (future web/app UIs will reuse the same domains and
would have had to fight `app/Filament`). The domains remain the center of gravity.

### 4.2 Shared Kernel / Core

```text
app/Core/
├── Audit/                       # audit driver + AuditLogger contract implementations
├── Contracts/                   # platform contracts (IdGenerator, TenantResolver, Clock…)
├── Enums/                       # platform-wide enums (PanelRole, TenantStatus)
├── Exceptions/                  # platform exceptions (TenantContextMissingException…)
├── Middleware/                  # EnsureTenantContext, EnsurePanelRole, InitTenancyForPanel…
├── Support/
│   ├── Helpers/                 # pure static helpers (money formatting, phone/IMEI…)
│   ├── Macros/                  # Str/Collection/Arr macros
│   └── Traits/                  # UsesUlid, HasTenantScope, SoftArchivable…
├── Tenancy/
│   ├── Central/                 # central-only concerns (models: Organization, Plan…)
│   │   └── Models/
│   ├── Tenant/                  # tenant bootstrap helpers
│   └── Jobs/                    # tenant lifecycle jobs (provision, snapshot, delete…)
└── Providers/
    ├── CoreServiceProvider.php      # ULID macro, global scopes, base bindings
    ├── AuditServiceProvider.php     # audit driver binding
    └── TenancyServiceProvider.php   # Stancl wiring (kept, but re-pointed to Organization)
```

### 4.3 The domain template (normative)

Every bounded context follows **exactly** this tree. Empty sub-folders are **forbidden** — a
layer folder is created only when it contains code (YAGNI).

```text
app/Domains/{Domain}/
├── Application/
│   ├── Actions/                # single-purpose use-case classes (D8)
│   ├── DTOs/                   # immutable input/transfer objects (already the pattern)
│   └── Services/               # workflow orchestration (already the pattern)
├── Domain/                     # pure domain — NO Eloquent, NO Laravel HTTP
│   ├── Contracts/              # interfaces owned by this domain
│   ├── Enums/                  # domain enums (backed)
│   ├── Events/                 # domain events (past-tense names)
│   ├── Exceptions/             # domain exceptions
│   ├── Traits/                 # domain traits
│   └── ValueObjects/           # rich value objects
├── Infrastructure/             # framework-coupled implementations
│   ├── Models/                 # Eloquent models of this domain (D3)
│   ├── Observers/              # model observers (only when a model lifecycle needs it)
│   ├── Policies/               # authorization policies
│   ├── Repositories/           # only when an abstraction is justified (§10)
│   └── Rules/                  # Laravel validation Rule objects
├── Presentation/
│   ├── Filament/
│   │   ├── Resources/          # domain-owned resources (D6)
│   │   ├── Pages/
│   │   └── Widgets/
│   └── Http/                   # controllers, requests, API resources for this domain
├── Jobs/                       # queue jobs belonging to this domain (if any)
├── Tests/                      # optional; primary home is tests/ mirrored here for proximity
├── {Domain}ServiceProvider.php # domain boot (D4)
└── README.md                   # vision, rules, workflows (governance)
```

### 4.4 Concrete example — Student domain

```text
app/Domains/Student/
├── Application/
│   ├── Actions/
│   │   ├── RegisterStudentAction.php
│   │   ├── EnrollStudentAction.php
│   │   └── TransferStudentAction.php
│   ├── DTOs/
│   │   └── StudentData.php
│   └── Services/
│       └── StudentService.php
├── Domain/
│   ├── Contracts/
│   │   └── StudentNumberGeneratorInterface.php
│   ├── Enums/
│   │   └── StudentStatus.php
│   ├── Events/
│   │   ├── StudentEnrolled.php
│   │   ├── StudentArchived.php
│   │   └── StudentTransferred.php
│   ├── Exceptions/
│   │   └── DuplicateStudentNumberException.php
│   └── ValueObjects/
│       └── StudentNumber.php
├── Infrastructure/
│   ├── Models/
│   │   └── Student.php
│   ├── Observers/
│   │   └── StudentObserver.php          # ULID, number generation trigger
│   ├── Policies/
│   │   └── StudentPolicy.php
│   └── Rules/
│       └── UniqueStudentNumber.php
├── Presentation/
│   └── Filament/
│       ├── Resources/
│       │   ├── StudentResource.php
│       │   └── StudentResource/
│       │       ├── Pages/
│       │       │   ├── ListStudents.php
│       │       │   ├── CreateStudent.php
│       │       │   └── EditStudent.php
│       │       └── RelationManagers/
│       │           └── EnrollmentsRelationManager.php
│       └── Pages/
│           └── StudentPortal.php
├── Jobs/
│   └── SendStudentWelcomeEmail.php
├── StudentServiceProvider.php
└── README.md
```

### 4.5 Panel structure

```text
app/Panel/
├── Base/
│   ├── BasePanelProvider.php       # shared middleware stack, branding, discovery OFF
│   ├── Traits/
│   │   └── RegistersDomainResources.php
│   └── Http/
│       └── Middleware/
│           ├── EnsureTenantContext.php
│           └── EnsurePanelRole.php
├── SuperAdmin/                     # CENTRAL panel (no tenant middleware)
│   ├── SuperAdminPanelProvider.php
│   ├── Pages/
│   └── Widgets/
├── SchoolAdmin/
├── Director/
├── Secretary/
├── Accountant/
├── Teacher/
├── Student/
└── Parent/                         # tenant panels — each with Pages/Widgets + own provider
```

Panels register **only** domain-owned resources via `->resources([...])` and set
`->discoverResources(...)` to an empty path (D6), guaranteeing zero accidental discovery and zero
duplication.

### 4.6 Tests structure

```text
tests/
├── Unit/
│   ├── Core/                      # helpers, traits, enums, middleware unit tests
│   └── Domains/
│       └── Student/
│           ├── Domain/            # value objects, enums, domain events (no DB)
│           └── Application/       # DTO mapping, action orchestration (mocked infra)
├── Feature/
│   ├── Domains/
│   │   └── Student/               # service/action integration against tenant schema
│   ├── Panels/
│   │   ├── SchoolAdmin/           # resource CRUD, permission gating
│   │   └── SuperAdmin/            # central panel, SaaS resources
│   └── Api/
│       └── V1/                    # tenant API
├── Tenancy/
│   ├── TenantLifecycleTest.php    # create → migrate → seed → destroy
│   ├── DataIsolationTest.php      # no cross-tenant reads
│   ├── PanelAccessTest.php        # role/panel matrix
│   └── BoundaryTest.php           # central never sees tenant tables, and vice-versa
└── Pest.php
```

---

## 5. DDD Diagram (text)

### 5.1 Layered view (Clean Architecture)

```text
                 ┌──────────────────────────────────────────────────────────┐
                 │                 PRESENTATION LAYER                       │
                 │  app/Panel/*  ·  app/Domains/*/Presentation  ·  routes/  │
                 │  Filament panels, pages, widgets, resources, HTTP, API   │
                 └───────────────▲──────────────────────┬───────────────────┘
                                 │                      │
                                 │  uses                │  dispatches commands/events
                 ┌───────────────┴──────────────────────▼───────────────────┐
                 │               APPLICATION LAYER                          │
                 │  app/Domains/*/Application                                │
                 │  Actions (use cases) · Services (orchestration) · DTOs    │
                 └───────────────▲──────────────────────┬───────────────────┘
                                 │ depends on          │ implements
                 ┌───────────────┴──────────────────────▼───────────────────┐
                 │                  DOMAIN LAYER (pure)                     │
                 │  app/Domains/*/Domain                                     │
                 │  Contracts · Enums · Events · ValueObjects · Exceptions   │
                 └───────────────▲──────────────────────┬───────────────────┘
                                 │ implements          │ consumed by
                 ┌───────────────┴──────────────────────▼───────────────────┐
                 │              INFRASTRUCTURE LAYER                        │
                 │  app/Domains/*/Infrastructure  ·  app/Infrastructure      │
                 │  Eloquent models · repositories · policies · queue · pdf  │
                 └───────────────▲──────────────────────────────────────────┘
                                 │
                 ┌───────────────┴──────────────────────────────────────────┐
                 │              SHARED KERNEL (app/Core + Domains/Shared)   │
                 │  audit · tenancy · middleware · traits · contracts        │
                 └──────────────────────────────────────────────────────────┘
```

Dependency rule: arrows point **downward only**. Nothing above may be imported below; the Domain
layer never imports Eloquent, HTTP, or Filament.

### 5.2 Dependency-flow view (request path)

```text
User request
   │  web/tenant panel route
   ▼
Middleware chain: EnsureTenantContext → EnsurePanelRole → Filament stack
   ▼
Filament Resource (Presentation)
   │  builds DTO from form state (Schema → StudentData)
   ▼
Domain Action / Service (Application)        ── validates via Rules + ValueObjects
   │  authorizes via Policy (Infrastructure)   (Gate::authorize)
   │  persists via Eloquent model (Infrastructure)
   │  dispatches domain events
   ▼
Domain Events (Domain) → Listeners (Application or Infrastructure)
   │  e.g. StudentEnrolled → SendWelcomeEmailJob (queued) + AuditLogger
   ▼
Cross-domain reactions stay event-driven — no direct imports between domains.
```

### 5.3 Context map (bounded contexts and their relationship)

```text
          ┌────────────────────────  CENTRAL (app: super-admin, SaaS)  ───────────────────────┐
          │  Billing       Plan/Subscription/Invoice            (event: TenantProvisioned)     │
          │  Identity      PlatformUser                         (event: PlatformUserCreated)   │
          │  SaaS          Organization · Domain · Provisioning                               │
          └───────────────────────────────────┬──────────────────────────────────────────────┘
                                              │  TENANT DATABASE (organization per DB)
          ┌───────────────────────────────────▼──────────────────────────────────────────────┐
          │                        ┌── ACADEMIC ──────────────────────────────────┐          │
          │   ┌──────────────┐     │  AcademicYear·Term · Program · GradeLevel   │          │
          │   │  IDENTITY    │     │  Section · Class · Subject · Timetable      │          │
          │   │  User · Role │     └──────▲─────────────────────┬───────────────┘          │
          │   │  Permission  │            │                     │                          │
          │   └──────▲───────┘     ┌──────┴───────┐       ┌─────▼──────────────┐            │
          │          │             │   STUDENT    │       │     GRADEBOOK      │            │
          │   ┌──────┴───────┐     │  Profile     │◄──────┤  Assessment · Result│            │
          │   │  GUARDIAN    │     │  Enrollment  │       │  Ranking · Transcript            │
          │   └──────▲───────┘     └──────▲───────┘       └─────────────────────┘            │
          │          │                    │                                                 │
          │   ┌──────┴───────┐     ┌──────┴───────┐        ┌──────────────┐                 │
          │   │  ATTENDANCE  │◄────┤   HR (Staff)  │◄───────┤  FINANCE     │                 │
          │   └──────▲───────┘     └──────▲───────┘        │  Fees · Payment│               │
          │          │                    │                └──────▲───────┘                 │
          │   ┌──────┴───────┐     ┌──────┴───────┐             │                          │
          │   │ COMMUNICATION│◄────┤  TRANSPORT   │      ┌───────┴───────┐                 │
          │   │ Messaging ·   │     └─────────────┘      │  EXAMINATION  │                 │
          │   │ Announcements │                         └───────────────┘                 │
          │   └──────────────┘                                                             │
          │   (Library · Hostel · Inventory · Reporting · Analytics · API — same star shape) │
          └──────────────────────────────────────────────────────────────────────────────────┘
```

Rule: **no edge between two context boxes** in the tenant map is a *direct code import*. Edges are
either (a) an event published by one and consumed by another, or (b) a shared kernel type
(`Domains/Shared`, `app/Core`).

---

## 6. Dependency Rules

Normative, enforced by architecture tests (§6.3).

### 6.1 Layer rules (Clean Architecture)

| From \ To | Presentation | Application | Domain | Infrastructure | Core/Shared |
|---|---|---|---|---|---|
| Presentation | ✅ | ✅ | ✅ (read-only VOs/enums/events) | ✅ (via Application only) | ✅ |
| Application | ❌ | ✅ | ✅ | ✅ (interfaces only) | ✅ |
| Domain | ❌ | ❌ | ✅ | ❌ | ✅ (Shared Kernel only) |
| Infrastructure | ❌ | ✅ (implements) | ✅ (implements) | ✅ | ✅ |
| Core/Shared | ❌ | ❌ | ❌ | ❌ | ✅ |

Hard rules:
1. **Domain layer imports nothing from Laravel Eloquent/HTTP/Filament.** It may import
   `Illuminate\Contracts\*` interfaces and PHP native types only.
2. **Domains never import each other's application/services directly.** Cross-domain behavior is
   event-driven (publish/consume) or mediated by an Application-level orchestrator.
3. **Presentation (Filament) never contains business logic.** Forms call Actions/Services.
4. **Everything may depend on the Shared Kernel** (`app/Core`, `app/Domains/Shared`), which must
   stay free of business logic.

### 6.2 Tenancy dependency rules

1. Central code never imports tenant models; tenant code never imports central models.
2. Only the tenancy bootstrappers and `TenancyServiceProvider` may touch `tenancy()` directly.
3. A tenant domain may receive **tenant ID / context** via the Shared Kernel
   (`TenantContext`), never by importing the tenant model.
4. Cross-context needs are handled by the SaaS domain via events, never by direct DB access.

### 6.3 Enforcement (architecture tests)

- A Pest test iterates all PHP files under `app/Domains/*/Domain` and asserts the file's `use`
  statements never reference `Illuminate\Database\Eloquent`, `Filament\*`, `App\Panel`,
  or `App\Domains\*` (other than `App\Domains\Shared` and sibling enums/events of the same domain).
- A test asserts no `app/Domains/*/Domain` file references another domain's `Application` or
  `Infrastructure`.
- A test asserts central migrations never appear under `database/migrations/tenant/` and vice-versa
  (by scanning table prefixes in migration bodies).
- These tests run in CI and locally via `composer test`.

---

## 7. Domain Boundaries

### 7.1 Canonical domain list

**Tenant domains (business):**

| Domain | Responsibility | Models (owned) | Depends on (events/shared) |
|---|---|---|---|
| `Identity` | users, roles, permissions, teams, profile | `User`, `Role`, `Permission`, `Team`, `UserRole` | — |
| `Student` | student profile, enrollment, transfer, lifecycle | `Student`, `StudentEnrollment` | `Academic` (enroll into class) |
| `Guardian` | guardians & parent linkage | `Guardian` | `Identity`, `Student` |
| `Academic` | year, term, program, grade level, section, class, subject, timetable | `AcademicYear`, `AcademicTerm`, `Program`, `GradeLevel`, `Section`, `AcademicClass`, `Subject`, `SubjectCoefficient`, `Classroom`, `TimetableEntry` | — |
| `Staff` (HR) | staff, contracts, departments, assignments | `Staff`, `Department`, `StaffContract`, `StaffAssignment` | `Identity` |
| `Attendance` | attendance records, sessions, reasons | `AttendanceRecord`, `AttendanceSession` | `Student`, `Academic` |
| `Gradebook` | assessments, results, grading scales | `Assessment`, `AssessmentResult`, `GradeScale` | `Student`, `Academic` |
| `Examination` | exam cycles, papers, supervision, marks entry | `ExamCycle`, `ExamPaper`, `ExamEntry`, `ExamSupervisor` | `Gradebook`, `Student` |
| `Finance` | fee structures, invoices, payments, arrears | `FeeStructure`, `Invoice`, `Payment`, `PaymentAllocation`, `FeeCategory` | `Student` |
| `Library` | catalog, copies, loans, reservations, fines | `Book`, `BookCopy`, `BookLoan`, `Reservation`, `Fine`, `Author`, `Publisher`, `BookCategory` | `Student`, `Staff` |
| `Transport` | fleet, drivers, routes, trips, assignments | `Vehicle`, `Driver`, `Route`, `RouteStop`, `Trip`, `StudentTransportAssignment` | `Student` |
| `Hostel` | residences, rooms, allocation, bed management | `Hostel`, `Room`, `Bed`, `HostelAllocation` | `Student` |
| `Inventory` | items, stock, requisitions, issuances | `InventoryItem`, `StockMovement`, `Requisition` | `Finance` |
| `Communication` | messages, announcements, notifications, templates, channels | `Message`, `MessageRecipient`, `Announcement`, `Notification`, `NotificationTemplate`, `NotificationChannel`, `CommunicationChannel` | all |
| `Reporting` | report definitions, generation, exports | `Report`, `ReportRun`, `ReportSchedule` | all (reads only) |
| `Analytics` | dashboards, widgets, KPIs, snapshots | `Dashboard`, `DashboardWidget`, `Kpi`, `AnalyticsSnapshot` | all (reads only) |
| `Api` | tenant API keys, webhooks, audit of API calls | `ApiClient`, `ApiToken`, `Webhook`, `WebhookDelivery` | — |

**Central/SaaS domains:**

| Domain | Responsibility |
|---|---|
| `SaaS` | `Organization` (tenant), `Domain`, `Plan`, `Subscription`, `FeatureFlag` |
| `Billing` | invoices to organizations, payments, dunning |
| `PlatformIdentity` | platform staff, super-admins (separate from tenant users) |

**Platform/infrastructure domains (not business):**
`System` (settings, backups, health, maintenance windows), `Plugin` (plugin registry),
`Ai` (AI provider integration — cross-cutting, may later become a product domain).

### 7.2 Domain integrity rules

1. A model belongs to exactly one domain. Cross-domain foreign keys are represented as bare IDs
   + read-model projections, or as event-sourced data.
2. Domains are allowed to read another domain's models **only** through a contract exposed by
   that domain (a read-model interface in `Domain/Contracts`), never through `use` of the model.
3. Aggregates: the root aggregate per domain is the model that owns the lifecycle
   (e.g., `Student` owns enrollments; `Invoice` owns payment allocations).
4. No domain may contain another domain's `Models`, `Services`, or `Resources`.

---

## 8. Naming Conventions

### 8.1 Files & classes

| Kind | Pattern | Example |
|---|---|---|
| Domain folder | Singular `PascalCase` | `Student`, `Gradebook`, `Communication` |
| Model | `PascalCase`, singular | `Student`, `AcademicClass` |
| DTO | `<Verb or Noun>Data` | `StudentData`, `EnrollStudentData` |
| Value Object | `PascalCase`, a concept | `StudentNumber`, `Money`, `EmailAddress` |
| Action | `<Verb><Noun>Action` | `RegisterStudentAction`, `TransferStudentAction` |
| Service | `<Noun>Service` | `StudentService`, `FleetService` |
| Event | past-tense `<Noun><Verb>` or `<Verb>ed` | `StudentEnrolled`, `InvoicePaid` |
| Listener | `<Verb><Noun>` | `SendEnrollmentEmail`, `NotifyGuardians` |
| Policy | `<Noun>Policy` | `StudentPolicy` |
| Rule (validation) | `<Constraint>Rule` | `UniqueStudentNumber` |
| Contract (interface) | `<Noun><Interface>` | `StudentNumberGeneratorInterface` |
| Repository | `<Noun>Repository` + impl `Eloquent<Noun>Repository` | `EnrollmentRepository` |
| Exception | `<Concept>Exception` | `DuplicateStudentNumberException` |
| Enum | `PascalCase`, backed values `snake_case` | `StudentStatus::Active` |
| Job | `<Verb><Noun>` | `SendStudentWelcomeEmail` |
| Model trait | `Uses<Concern>` | `UsesUlid`, `HasTenantScope` |

### 8.2 Databases

| Item | Convention |
|---|---|
| Table | `snake_case` plural, domain-prefixed when ambiguity possible (`student_enrollments`) |
| Columns | `snake_case`; tenant discriminator is `school_id`; FK = `{singular_table}_id` |
| PK | `id` ULID string (D9) |
| Status columns | `status` with domain backed enum cast |
| Soft delete | `deleted_at` |
| Audit | `created_by`, `updated_by` (nullable), plus central `audit_logs` for critical ops |

### 8.3 PHP / code style

- PSR-12, enforced by Pint (`pint.json` keeps `declare_strict_types: true`, alpha-sorted imports).
- PHPStan level 6 (raised from 5) on `app`.
- No docblocks repeating types; typed signatures everywhere. `readonly` classes for DTOs.
- No `Auth::` facades inside domain `Application/` code — the acting user is passed explicitly.

---

## 9. Service Layer Strategy

**Role:** Services orchestrate business workflows spanning several steps, aggregates, or domains.
They sit in `Application/Services/`, are **stateless**, resolved from the container, and compose
Actions. They never contain pure query passthrough.

### 9.1 Rules

1. **A Service exists only for multi-step workflows.** CRUD-only cases use Actions or the model
   directly (through the domain). No empty services.
2. Services authorize (`Gate::authorize`), validate (Rules/ValueObjects), orchestrate
   transactions (`DB::transaction`), and dispatch domain events.
3. Services receive DTOs, never raw request arrays (except in thin web adapters).
4. Query concerns (`paginate`, `list`) stay in read-model / repository or query methods on the
   model — NOT in services.
5. Cross-domain orchestration at the application edge uses a coordinator service that consumes
   contracts, not models.

```php
// app/Domains/Student/Application/Services/StudentService.php
final class StudentService
{
    public function __construct(
        private readonly RegisterStudentAction $register,
        private readonly EnrollStudentAction $enroll,
    ) {}

    public function admit(StudentData $data, User $actor): Student
    {
        Gate::forUser($actor)->authorize('create', Student::class);

        return DB::transaction(function () use ($data, $actor) {
            $student = $this->register->execute($data, $actor);
            $this->enroll->execute($student, $data->enrollment, $actor);

            StudentAdmitted::dispatch($student, $actor);

            return $student;
        });
    }
}
```

---

## 10. Repository Strategy

**Decision:** no blanket repository layer. Eloquent models are the persistence abstraction
(consistent with the existing v0.9 review, which explicitly rejected empty repositories).

### 10.1 When a repository IS introduced (and only then)

1. The domain needs a pure, testable persistence contract that Eloquent cannot satisfy at the
   Domain layer (e.g., aggregate root load/save semantics).
2. An external data source (API, cache-backed store) must be swappable.
3. Query logic is complex and reused by multiple Application services.

### 10.2 Shape

- Interface lives in `Domain/Contracts/` (e.g., `EnrollmentRepositoryInterface`).
- Implementation lives in `Infrastructure/Repositories/EloquentEnrollmentRepository.php`.
- The domain provider binds interface → implementation.
- Repositories expose **domain methods** (`findActiveForStudent(StudentNumber $number)`), never
  leak Eloquent query builder types.

```php
// Domain/Contracts/EnrollmentRepositoryInterface.php
interface EnrollmentRepositoryInterface
{
    /** @return list<Enrollment> */
    public function currentForStudent(StudentId $studentId): array;
}
```

```php
// Infrastructure/Repositories/EloquentEnrollmentRepository.php
final class EloquentEnrollmentRepository implements EnrollmentRepositoryInterface
{
    public function currentForStudent(StudentId $studentId): array
    {
        return StudentEnrollment::query()
            ->forSchool(tenant()->school_id)
            ->where('student_id', $studentId->value)
            ->whereNull('completed_at')
            ->get()
            ->all();
    }
}
```

---

## 11. DTO Strategy

The existing `readonly` DTO pattern is **kept and made normative**.

### 11.1 Rules

1. DTOs are `final readonly`, constructed with **named arguments**.
2. They expose `fromArray()` (input mapping) and `toArray()` (persistence mapping) when needed;
   otherwise they carry plain typed properties.
3. DTOs are **immutable**; services return entities/models, never mutate the DTO.
4. A DTO is created at the application boundary (Filament form hook, controller) and passed into
   Actions/Services. It is the **only** input type for Application code.
5. DTOs do not validate; validation happens in Rules/ValueObjects at the Action boundary.

```php
final readonly class EnrollStudentData
{
    public function __construct(
        public string $studentId,
        public string $classId,
        public string $academicYearId,
        public ?\Carbon\CarbonInterface $effectiveDate = null,
    ) {}
}
```

---

## 12. Action Pattern

**Role:** Actions are the smallest reusable unit of a use case — one public `execute()`, no
state, container-resolved.

### 12.1 Rules

1. One class per operation; class name reads like an imperative sentence (`RegisterStudent`).
2. Public API is a single method `execute(...)` (variants: `handle()` allowed only in Jobs).
3. Actions are **stateless**; injected collaborators are constructor dependencies.
4. An Action returns a model/DTO/`void` and may dispatch domain events — but a **service**
   owns the transaction when multiple actions must commit atomically.
5. Actions validate invariants, but leave cross-aggregate orchestration to Services.

```php
// Application/Actions/RegisterStudentAction.php
final class RegisterStudentAction
{
    public function __construct(
        private readonly StudentNumberGeneratorInterface $numberGenerator,
        private readonly StudentRepositoryInterface $students,
    ) {}

    public function execute(StudentData $data, User $actor): Student
    {
        $number = $this->numberGenerator->next($data->schoolId);

        return $this->students->persist(
            Student::factory()->admitted(
                schoolId: $data->schoolId,
                number: $number,
                actor: $actor,
            )
        );
    }
}
```

### 12.2 Decision: Action vs Service vs Job

| Concern | Use |
|---|---|
| One operation, no orchestration | **Action** |
| Multi-step workflow, transaction spanning several aggregates | **Service** (composing Actions) |
| Delayed / retryable side effect | **Job** (dispatched from event or service) |
| Query / read list | model query / read-model, not Action/Service |

---

## 13. Event Strategy

### 13.1 Principles

1. **Domain events** live in `Domain/Events/`, named in the past tense, carrying context
   (entity + actor). They are the **only** cross-domain coupling mechanism.
2. **Listeners** are per-domain, registered in the domain's own provider. The central
   `EventServiceProvider` god-object is deleted (D5).
3. **Audit is infrastructure, not per-event listeners.** One `AuditLogger` listener subscribed to
   a *set* of events via `ShouldQueue`/tags, or an observer-based audit driver, replaces the
   dozens of `Log*` listeners.
4. Side effects that must not slow the request are dispatched as **queued** events
   (`ShouldQueue` listeners or jobs).
5. Read-model propagation (e.g., Analytics consuming events) uses dedicated listeners in the
   consuming domain, never direct calls.

### 13.2 Example — per-domain registration

```php
// app/Domains/Student/StudentServiceProvider.php
final class StudentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StudentNumberGeneratorInterface::class, IncrementingStudentNumberGenerator::class);
        $this->app->bind(StudentRepositoryInterface::class, EloquentStudentRepository::class);
    }

    public function boot(): void
    {
        Event::listen(StudentEnrolled::class, SendEnrollmentNotification::class);
        Event::listen(StudentEnrolled::class, CreateFinanceAccount::class); // Communication & Finance consume
        Gate::policy(Student::class, StudentPolicy::class);
    }
}
```

**Events the Audit infrastructure subscribes to** are declared once in `AuditServiceProvider` via
an `auditable_events` config list — not as N hand-written listener mappings.

### 13.3 Event list (canonical subset)

- `StudentAdmitted`, `StudentEnrolled`, `StudentTransferred`, `StudentArchived`
- `InvoiceIssued`, `PaymentRegistered`, `PaymentAllocated`
- `AttendanceSessionClosed`, `AssessmentResultsPublished`
- `BookLoanOverdue`, `TripCompleted`
- `OrganizationProvisioned`, `SubscriptionChanged`, `TenantSuspended` (central)

---

## 14. Policy Strategy

### 14.1 Principles

1. Policies are per-domain, in `Infrastructure/Policies/`, registered by the domain provider
   (or by convention: `App\Domains\*\Infrastructure\Policies\*Policy` auto-discovery at boot).
2. Authorization = **role gating at panel level** (who can reach a panel) + **permission gating
   at resource level** (who can act on a resource) + **tenant/school scoping** (data-level).
3. A shared trait `InteractsWithTenant` on `User` provides `hasRole/hasPermissionTo` and
   `isInSchool($schoolId)` so policies do not duplicate tenancy checks.
4. `super_admin` bypass is restricted to the **central** super-admin panel; inside tenant
   panels, super_admin has no implicit override (D7).
5. Policies are the ONLY place that decides "can this actor do this to this resource".
   Actions/services call `Gate::authorize`; Filament uses `can()`/`authorizeAccess()`.

```php
// Infrastructure/Policies/StudentPolicy.php
final class StudentPolicy
{
    public function view(User $user, Student $student): bool
    {
        return $user->isInSchool($student->school_id)
            && $user->hasPermissionTo('student.view');
    }

    public function update(User $user, Student $student): bool
    {
        return $user->isInSchool($student->school_id)
            && $user->hasPermissionTo('student.update');
    }

    public function forceDelete(User $user, Student $student): bool
    {
        return false; // never
    }
}
```

### 14.2 Permission naming

`{resource}.{action}` where action ∈ `view, create, update, delete, restore, publish, approve,
pay, transfer, ...` — e.g., `student.view`, `invoice.approve`, `attendance.record`.

---

## 15. Infrastructure Overview

```text
app/Infrastructure/
├── Audit/            # AuditLogger driver, writes to audit_logs; fired by config-listed events
├── Filesystem/       # disk abstraction, tenant-aware paths, signed uploads
├── Notifications/    # channel adapters (sms, push, email) behind NotificationGateway contract
├── Pdf/              # PdfRenderer (dompdf/spatie) behind PdfRenderer contract
├── Queue/            # queue config, unique-job helpers, rate-limited jobs
├── Search/           # (future) search index adapter — contract first
├── Storage/          # media/blob handling, tenant storage root
└── Cache/            # tenant-aware cache keying helpers
```

Infrastructure packages are framework-coupled, cross-domain, and depend on `app/Core/Contracts`
only. Domains depend on **contracts** defined either in their own `Domain/Contracts` or in
`app/Core/Contracts`; never on concrete infrastructure classes.

**Selected contracts (normative):**

```php
// app/Core/Contracts/PdfRendererInterface.php
interface PdfRendererInterface
{
    public function render(string $view, array $data): BinaryFileResponse|string;
}
```

```php
// app/Core/Contracts/NotificationGatewayInterface.php
interface NotificationGatewayInterface
{
    public function send(NotificationMessage $message, NotificationRecipient $to): bool;
}
```

---

## 16. Shared Components

### 16.1 `app/Core` (platform shared kernel)
- `UsesUlid` trait (single ULID generator — D9).
- `HasTenantScope` trait (automatic `school_id` scoping for tenant models).
- `EnsureTenantContext` middleware, `EnsurePanelRole` middleware.
- `TenantContext` value object (resolved from the middleware, injected into services).
- `AuditLogger` contract + driver.
- `Money`/`EmailAddress`-style support value objects live in `app/Core/Support` only if
  cross-domain; domain-specific VOs live in their domain.

### 16.2 `app/Domains/Shared` (domain shared kernel)
- Cross-domain enums that several business domains legitimately share (e.g.,
  `ActiveArchivedStatus`, `SchoolEntityStatus`).
- Cross-domain traits (e.g., `HasEntityExtraction`, `UniqueValidator` — already present).
- Shared **events** are avoided; shared **contracts** preferred.

### 16.3 Shared event helpers
- `Dispatchable` events implement `ShouldBroadcast`/`ShouldQueue` when appropriate.
- A shared `DomainEvent` interface for audit tagging.

---

## 17. Future Module Integration Strategy

Any of the planned modules (Academic, Students, Teachers, Parents, Finance, Attendance,
Examinations, Library, Transport, Hostel, Inventory, HR, Messaging, Reports, API, Mobile,
Analytics) integrate by the same recipe:

1. **Create the bounded context** under `app/Domains/{Module}/` using the domain template (§4.3).
2. **Domain layer first** — enums, value objects, events, contracts. No Eloquent yet.
3. **Infrastructure** — models (with `UsesUlid` + `HasTenantScope`), policies, rules, repositories
   (only if justified).
4. **Application** — actions/services/DTOs implementing the workflows.
5. **Presentation** — Filament resources owned by the domain.
6. **Wire it** — `{Module}ServiceProvider` registers events, bindings, policies; the tenant
   panels explicitly add the resources (and the panels' permission matrix is extended).
7. **Tenant migration** in `database/migrations/tenant/` — never central.
8. **Tests** — domain unit tests (no DB), feature tests (tenant DB), panel tests.
9. **Documentation** — domain `README.md` + permission matrix update.

Cross-cutting modules (API, Mobile, Analytics) are built **as consumers**: they read via
read-model contracts or events, never importing sibling domains' internals.

**Plugin/Marketplace:** the existing `Plugin` infrastructure is kept as-is (registry +
discovery), but any *business* module is a first-class domain, not a plugin. Plugins are reserved
for third-party extensions.

---

## 18. Migration Strategy

### 18.1 Step 0 — Baseline correction (foundation, THIS sprint's concern)
- Re-point `config/tenancy.php` `tenant_model` to `Organization` (D1).
- Move all business migrations from `database/migrations/` to `database/migrations/tenant/`.
  Central keeps only: `tenants`, `domains`, `plans`, `subscriptions`, central `users`,
  `audit_logs`, `system_settings`, `jobs`/`failed_jobs`/`cache`/`sessions` are tenant-aware and
  can stay central, plus `personal_access_tokens` (central) if API is central.
- Table owners: `schools` and business tables become **tenant** tables; the redundant
  `schools` central table is removed (its data re-homed as a tenant record per organization).

### 18.2 Step 1 — Schema identity
- Convert all PKs to ULID strings; remove auto-increment `uuid` columns and the global
  `Model::creating` hook (D9). Run as batched, downtime-windowed migrations.

### 18.3 Step 2 — Tenant adoption (data re-homing)
- For each existing organization: create tenant DB → run tenant migrations → copy business rows,
  mapping `school_id` → `school_id` inside the tenant DB.
- Keep `school_id` as the intra-tenant discriminator (multi-campus support).

### 18.4 Step 3 — Roll forward
- New features ship exclusively as tenant migrations.
- A CI test fails if any PR adds a table to central migrations that is not in the central allowlist.

### 18.5 Migration file conventions
- One migration file per concern; `Schema::create` inside `database/migrations/tenant/` must be
  marked with `--tenant` in name prefix when generated (`php artisan tenants:migrate` is used).
- No business logic in migrations; data backfills are seeders/commands, not migrations.

---

## 19. Risks

| # | Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|---|
| R1 | Tenant re-homing (Step 2) breaks data | Med | High | Dry-run tooling, per-tenant shadow verification, rollback snapshots, freeze window |
| R2 | Moving 84 models into domains breaks ~220 tests + Filament references | High | Med | Mechanical move with Pint/PHPStan, run suite in one commit, keep old namespaces via aliases during transition |
| R3 | Filament v4 vs installed v5 divergence | Med | Med | Document v5 as the frozen version; architecture principles identical; pin `^5.6` |
| R4 | PHP 8.4 adoption blocks local Windows tooling | Med | Low | Containerize dev (Sail/HerD); CI matrix on 8.4 |
| R5 | Over-engineering (empty layer folders, repositories everywhere) | Med | Med | YAGNI rule: folders exist only with content; repositories only on justification |
| R6 | Session-based tenancy remaining in production | Med | High | Move to domain/path-based identification (D7) before scale-out |
| R7 | Audit-by-event grows listeners again | Med | Low | Config-driven audit subscriber set; no per-event `Log*` classes |
| R8 | Team culture drifts after freeze | High | Med | Architecture tests in CI (PHPStan + Pest) enforce rules mechanically |
| R9 | Central super-admin can reach tenant panels | Low | High | `canAccessPanel` must require a tenant context for tenant panels (D7) |

---

## 20. Recommendations

1. **Execute this report as the Architecture Freeze v2.0.** Replace the v1.0 ADR's "frozen flat
   structure" with the layered per-domain structure above.
2. **Foundation first, in this order:**
   1. Re-point tenancy to `Organization` + split migrations (D1/D2) — no business features.
   2. Introduce `app/Core` + `UsesUlid`/`HasTenantScope` + middleware (D7/D9).
   3. Move models into domains and introduce per-domain providers (D3/D4) — done as one
      mechanical, test-verified commit.
   4. Delete `EventServiceProvider` god-object; wire audit infrastructure (D5).
   5. Restructure panels to domain-owned resources + `BasePanelProvider` (D6).
   6. Add Pest + tenancy test suite + architecture tests (D11, §6.3).
3. **Bump the stack:** `composer.json` `php: "^8.4"`, add `pestphp/pest` to require-dev, keep
   Filament `^5.6` (documented deviation from the v4 spec), raise PHPStan to level 6.
4. **Freeze this document.** Any structural change requires a new ADR (per existing governance).
5. **Begin SPR-002+ with a single domain** (recommended: `Student`) implemented end-to-end using
   this template as the reference implementation, proving the pattern before scaling across domains.

---

## Annex A — Panel Architecture

### A.1 Final panel matrix

| Panel | Context | Tenant middlewares | Owned by | Registers (explicit) |
|---|---|---|---|---|
| `SuperAdmin` | Central | none | `app/Panel/SuperAdmin` | SaaS/Platform resources only |
| `SchoolAdmin` | Tenant | `EnsureTenantContext`, `EnsurePanelRole:school_admin` | `app/Panel/SchoolAdmin` | Academic, Identity, Student, Finance(overview) |
| `Director` | Tenant | same, `director` | `app/Panel/Director` | Reporting, Gradebook, Finance, Staff |
| `Secretary` | Tenant | same, `secretary` | `app/Panel/Secretary` | Student, Guardian, Communication, Attendance |
| `Accountant` | Tenant | same, `accountant` | `app/Panel/Accountant` | Finance only |
| `Teacher` | Tenant | same, `teacher` | `app/Panel/Teacher` | Gradebook, Attendance, Timetable |
| `Student` | Tenant | same, `student` | `app/Panel/Student` | own profile, timetable, results (read) |
| `Parent` | Tenant | same, `parent` | `app/Panel/Parent` | own children, payments, communication |

Rules:
- **No duplicated resources.** A resource is defined once in its owning domain and referenced by
  the panels that may show it.
- **No accidental auto-discovery.** `->discoverResources(...)` points at an empty/nonexistent path
  on every panel; only explicitly listed resources load.
- **Pages/Widgets** are panel-owned (they are presentation-only) — `app/Panel/{Panel}/Pages`.
- **Resources/RelationManagers** are domain-owned — `app/Domains/{Domain}/Presentation/Filament`.

### A.2 Base panel provider (kills the copy-pasted middleware)

```php
// app/Panel/Base/BasePanelProvider.php
abstract class BasePanelProvider extends PanelProvider
{
    abstract protected function panelId(): string;
    abstract protected function panelPath(): string;

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id($this->panelId())
            ->path($this->panelPath())
            ->login()
            ->discoverResources(in: app_path('Panel/__none'), for: 'App\\Panel\\__none')
            ->middleware([... /* single shared stack */])
            ->authMiddleware([Authenticate::class]);
    }
}
```

### A.3 Tenant panel wiring

```php
// app/Panel/Teacher/TeacherPanelProvider.php
final class TeacherPanelProvider extends BasePanelProvider
{
    protected function panelId(): string { return 'teacher'; }
    protected function panelPath(): string { return 'teacher'; }

    public function panel(Panel $panel): Panel
    {
        return parent::panel($panel)
            ->middleware([
                EnsureTenantContext::class,          // binds tenant at login, verified each request
                EnsurePanelRole::class.':teacher',
            ])
            ->resources([
                \App\Domains\Gradebook\Presentation\Filament\Resources\AssessmentResource::class,
                \App\Domains\Attendance\Presentation\Filament\Resources\AttendanceResource::class,
            ])
            ->pages([/* panel-owned pages */]);
    }
}
```

---

## Annex B — Multi-Tenancy Boundary Specification

### B.1 Identification
- **Tenant model:** `Organization` (extends `Tenant`). `id` = ULID.
- **Identification strategy:** `InitializeTenancyByDomain` (each organization owns domains) for
  SaaS portals; for panel-based access, tenant is bound at **login** and carried in the session,
  re-verified by `EnsureTenantContext` on every request. Path-based tenant id is **forbidden** in
  v1 (URL-leakage and cache-poisoning risk).
- `config/tenancy.php`: `tenant_model => App\Domains\SaaS\Models\Organization::class`.

### B.2 Data placement

| Data | Location |
|---|---|
| `organizations`, `domains`, `plans`, `subscriptions`, central `users` (platform staff), central `audit_logs`, `feature_flags` | **Central** |
| `schools`, `campuses` (organization structure) | **Tenant** |
| All business tables (students, finance, academic…) | **Tenant** |
| `cache`, `sessions`, `queues`, `files` | **Tenant-aware** (bootstrappers) |
| Cross-tenant reference data (currencies, countries, languages) | **Tenant** (seeded per tenant) |

### B.3 Isolation guarantees
1. Tenant connections switch the database; no central code runs tenant queries and vice-versa.
2. `HasTenantScope` forces `school_id` scoping on all tenant models that support multi-campus.
3. Queue jobs carry the tenant context (`tenancy()->initialize` in the job) and restore central
   after run.
4. Files are written under `{storage}/{tenant_id}/` via `FilesystemTenancyBootstrapper`.
5. Cache keys are tagged by tenant via `CacheTenancyBootstrapper`.

### B.4 Boundary events (central ⇄ tenant)
- `OrganizationProvisioned` → central creates DB + runs tenant migrations + seeds.
- `SubscriptionChanged`, `OrganizationSuspended` → tenants receive maintenance/read-only signal.
- Tenants never push to central; central reacts to its own lifecycle events only.

---

## Annex C — Security Review

### C.1 Authentication
- Tenant users authenticate in the **tenant** context (their panel). Central users (super-admins)
  authenticate in the **central** context.
- Passwords: default Laravel `hashed` cast + Argon2id where configured.
- Session bound to tenant: `EnsureTenantContext` writes `tenant_id` at login, compares it on every
  request, and rejects if the actor is not a member of that organization.
- Rate limiting on auth endpoints (`ThrottleRequests`) and on API (`CheckApiRateLimit` already
  exists — keep).

### C.2 Authorization
- Panel access: `User::canAccessPanel(Panel $panel)` — role matrix (§A.1) **plus** tenant-context
  requirement for tenant panels (R9).
- Resource access: policies + permissions (`spatie/laravel-permission`), resource `can()` checks.
- Role isolation: roles are tenant-scoped (per-DB); central roles never mix with tenant roles.
- Sensitive operations (payment, delete, transfer, publish grades) require `approve`/`delete`
  permissions AND are audited.

### C.3 Tenant isolation
- Enforced by DB separation + `HasTenantScope` + middleware (see Annex B).
- Tenant context is re-initialized on every request; jobs re-initialize explicitly.

### C.4 Sensitive data
- Environment secrets only via `.env`; no secrets committed (verify `git status` before commits).
- ULID primary keys avoid enumeration attacks vs sequential IDs.
- Signed URLs for file downloads; storage paths include tenant id.
- Soft deletes + audit for compliance; `forceDelete` disabled in policies.

### C.5 Audit readiness
- `audit_logs` records actor, action, tenant, entity, before/after, IP, user-agent.
- Critical events (auth, payment, delete, publish, permission changes) are written synchronously;
  high-volume events via queued audit.
- Audit is immutable (append-only table; no update/delete routes).

### C.6 Middleware order (tenant panel)
```text
web → EnsureTenantContext → EnsurePanelRole:{role} → Filament auth stack
```
`EnsureTenantContext` runs **after** session middleware (needs session) and **before** any
controller/resource code; `EnsurePanelRole` runs after context (needs `tenancy()` initialized).

---

## Annex D — Definition of Done — Validation

| Criterion | Status | Where demonstrated |
|---|---|---|
| Architecture is coherent | ✅ | §3–§6 (decisions + dependency rules) |
| Domain boundaries clearly defined | ✅ | §7 (canonical domains + integrity rules) |
| Folder structure finalized | ✅ | §4 (normative template + example) |
| Dependency rules documented | ✅ | §6 (matrix + hard rules + enforcement) |
| Multi-tenancy boundaries validated | ✅ | Annex B (identification, placement, isolation) |
| Panel architecture finalized | ✅ | Annex A (matrix, base provider, no auto-discovery) |
| Security considerations documented | ✅ | Annex C (auth, authorization, isolation, audit) |
| Ready to implement business modules | ✅ | §17 (integration recipe) + §18 (migration strategy) |

**Sprint is complete. SPR-002 may begin with the `Student` domain as reference implementation.**
