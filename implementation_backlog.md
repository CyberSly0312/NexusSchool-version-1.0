# Nexus School ERP — Complete Implementation Backlog

**Document ID:** NS-BACKLOG-001  
**Date:** 2026-07-14  
**Role:** Technical Lead  
**Scope:** Complete project implementation backlog based on documentation, UI template, and current codebase state.  

---

## 📋 Implementation Order Strategy

The backlog is structured to respect architectural dependencies. 
1. **Foundation & Architecture:** Base constraints, dependencies, and traits.
2. **Database Layer:** Migrations, seeders, and factories (bottom-up approach).
3. **SaaS / Multi-Tenancy:** Landlord DB, tenant scoping, and routing.
4. **Security & Auth:** Roles, permissions, and user access.
5. **UI / Filament Foundation:** Theme implementation and base resources.
6. **Core Engines:** Academic Framework -> Admissions -> Student Lifecycle -> Finance.
7. **APIs & Integrations:** Exposing the platform functionality.

---

## 🎯 Epic 1: Project Foundation & Architecture
**Priority:** Highest | **Dependencies:** None

### Feature 1.1: Environment & Dependency Setup
* **Task 1.1.1:** Configure Laravel Environment
  * *Subtask:* Update `.env.example` to enforce PostgreSQL (replace SQLite).
  * *Subtask:* Clean up default unused Laravel skeleton files.
* **Task 1.1.2:** Install Core Packages
  * *Subtask:* Install `filament/filament:^4.0`.
  * *Subtask:* Install `spatie/laravel-permission`.
  * *Subtask:* Install Tenancy package (`stancl/tenancy` or `spatie/laravel-multitenancy`).

### Feature 1.2: Base Architecture Patterns
* **Task 1.2.1:** Implement UUID/ULID Strategy
  * *Subtask:* Create base Traits for ULID primary keys.
  * *Subtask:* Ensure all foreign keys use ULID schema configuration.
* **Task 1.2.2:** Implement Soft Deletes Strategy
  * *Subtask:* Create custom base Model enforcing `SoftDeletes` and `HasUlids`.
* **Task 1.2.3:** Implement Audit Logging Pattern
  * *Subtask:* Install/configure activity logging package (e.g., `spatie/laravel-activitylog`).

---

## 🗄️ Epic 2: Database Layer & Data Modeling
**Priority:** Highest | **Dependencies:** Epic 1

### Feature 2.1: Reference Data Migrations (Phase 1)
* **Task 2.1.1:** Create global reference tables
  * *Subtask:* Migration: `countries`, `currencies`, `languages`.
  * *Subtask:* Migration: `education_levels`, `grading_scales`.

### Feature 2.2: Core Domain Migrations (Phase 2 & 3)
* **Task 2.2.1:** Multi-Tenancy & Security Tables
  * *Subtask:* Migration: `schools` (Tenants).
  * *Subtask:* Publish Spatie Permission migrations.
* **Task 2.2.2:** Academic Engine Migrations
  * *Subtask:* Migration: `academic_years`, `academic_terms`.
  * *Subtask:* Migration: `departments`, `programs`, `levels`, `sections`.
  * *Subtask:* Migration: `classes`, `subjects`.

### Feature 2.3: Entity Migrations (Phase 4 & 5)
* **Task 2.3.1:** People Data Migrations
  * *Subtask:* Migration: `teachers`, `students`, `parents`.
  * *Subtask:* Migration: Pivot tables (`student_subject`, `teacher_subject`).
* **Task 2.3.2:** Transactional Migrations
  * *Subtask:* Migration: `enrollments`, `fee_structures`, `invoices`, `payments`, `attendance_records`.

### Feature 2.4: Seeders & Factories
* **Task 2.4.1:** Clean existing technical debt
  * *Subtask:* Fix fatal syntax error in `DatabaseSeeder.php`.
  * *Subtask:* Remove hardcoded IDs in `AcademicYearFactory`.
* **Task 2.4.2:** Implement Reference Seeders
  * *Subtask:* Build mandatory Seeders (Roles, Countries, Currencies).

---

## 🏢 Epic 3: Multi-Tenancy & SaaS Core
**Priority:** High | **Dependencies:** Epic 1, Feature 2.2

### Feature 3.1: Landlord Architecture
* **Task 3.1.1:** Configure Database Connections
  * *Subtask:* Setup Landlord vs Tenant DB routing in `database.php`.
* **Task 3.1.2:** Tenant Management
  * *Subtask:* Implement `School` (Tenant) Model.
  * *Subtask:* Implement middleware for tenant domain isolation.

### Feature 3.2: Subscription Engine
* **Task 3.2.1:** Plan Management
  * *Subtask:* Implement `Plans` and `Subscriptions` tables/models.
* **Task 3.2.2:** School Onboarding Workflow
  * *Subtask:* Create automated tenant DB provisioning upon School registration.

---

## 🔐 Epic 4: Authentication & RBAC
**Priority:** High | **Dependencies:** Epic 2

### Feature 4.1: Role-Based Access Control
* **Task 4.1.1:** Define Core Roles
  * *Subtask:* Seed roles: `SuperAdmin`, `SchoolAdmin`, `Teacher`, `Student`, `Parent`.
* **Task 4.1.2:** Apply RBAC Traits
  * *Subtask:* Add `HasRoles` to `User` model.

### Feature 4.2: User Management
* **Task 4.2.1:** Fix User Model
  * *Subtask:* Add missing attributes (`tenant_id`, `deleted_at`).
  * *Subtask:* Create User Profile relationships.

---

## 🎨 Epic 5: UI/UX & Filament Integration
**Priority:** High | **Dependencies:** Epic 1 (Filament installed)

### Feature 5.1: MaterialM Theme System
* **Task 5.1.1:** Configure Admin Panel Provider
  * *Subtask:* Apply MaterialM color palette (`#00a2ff` primary, `#2a3547` dark, etc.) in `AdminPanelProvider.php`.
  * *Subtask:* Customize fonts (Plus Jakarta Sans) and typography.
* **Task 5.1.2:** Layout & Component Overrides
  * *Subtask:* Customize Filament sidebar, top bar, and widgets to match MaterialM dashboard layout.
  * *Subtask:* Override Filament form/card border radius and shadow tokens.

---

## 📚 Epic 6: Academic Framework Engine
**Priority:** Medium | **Dependencies:** Epic 2, Epic 5

### Feature 6.1: Academic Lifecycle Management
* **Task 6.1.1:** Academic Years & Terms
  * *Subtask:* Implement `AcademicYear` Filament Resource (with business rules: "only one active").
  * *Subtask:* Implement `AcademicTerm` Resource.

### Feature 6.2: Structural Management
* **Task 6.2.1:** Programs & Departments
  * *Subtask:* Implement `Department` and `Program` Resources.
* **Task 6.2.2:** Classes & Subjects
  * *Subtask:* Implement `Class` and `Subject` Resources.

---

## 📝 Epic 7: Admissions Engine
**Priority:** Medium | **Dependencies:** Epic 6

### Feature 7.1: Admission Pipeline
* **Task 7.1.1:** Admission Cycles
  * *Subtask:* Implement `AdmissionCycle` Resource.
* **Task 7.1.2:** Applications Workflow
  * *Subtask:* Implement Application form and state machine (Draft -> Submitted -> Reviewed -> Accepted/Rejected).

---

## 🎓 Epic 8: Student Engine
**Priority:** Medium | **Dependencies:** Epic 6, Epic 7

### Feature 8.1: Student Profiles
* **Task 8.1.1:** Resolve Technical Debt
  * *Subtask:* Move and refactor the stray `StudentResource.php` into `app/Filament/Resources/`.
* **Task 8.1.2:** Student Lifecycle
  * *Subtask:* Implement `Student` Model and relationships (`Parents`, `Classes`).
  * *Subtask:* Implement status transitions (Active, Suspended, Graduated).

### Feature 8.2: Daily Operations
* **Task 8.2.1:** Attendance & Timetables
  * *Subtask:* Implement `Attendance` recording UI.
  * *Subtask:* Implement `Timetable` generation algorithms.

---

## 💰 Epic 9: Finance Engine
**Priority:** Low | **Dependencies:** Epic 8

### Feature 9.1: Billing Operations
* **Task 9.1.1:** Fee Structures
  * *Subtask:* Create `FeeStructure` definition Resource.
* **Task 9.1.2:** Invoicing
  * *Subtask:* Implement background job for batch invoice generation.
  * *Subtask:* Implement `Payment` processing logic and receipt generation.

---

## 🔌 Epic 10: API & Integration Layer
**Priority:** Low | **Dependencies:** All previous Epics

### Feature 10.1: REST APIs
* **Task 10.1.1:** External Endpoints
  * *Subtask:* Build Student API endpoints (`/api/v1/students`).
  * *Subtask:* Build Finance API endpoints (`/api/v1/finance/invoices`).
* **Task 10.1.2:** API Security
  * *Subtask:* Implement Laravel Sanctum/Passport authentication.
  * *Subtask:* Enforce tenant scoping on all API routes.

---

## 🧪 Epic 11: Testing & QA
**Priority:** Continuous | **Dependencies:** Parallel to execution

### Feature 11.1: Automated Testing
* **Task 11.1.1:** Test Suite Setup
  * *Subtask:* Configure PHPUnit / Pest for PostgreSQL testing DB.
* **Task 11.1.2:** Domain Tests
  * *Subtask:* Write unit tests for Academic Framework business rules (e.g., overlapping terms).
  * *Subtask:* Write feature tests for Admission State Machine.

---

## 📊 Summary of Effort & Risk

| Category | Estimated Complexity | Primary Risk | Mitigation |
|----------|----------------------|--------------|------------|
| **Architecture** | High | Multi-tenancy leaks | Strict Global Scopes and Middleware |
| **Database** | High | PostgreSQL vs SQLite dev drift | Enforce Postgres via Docker/Sail |
| **UI/UX** | Medium | Filament limitation for custom UI | Use custom Blade views inside Filament widgets |
| **Business Logic** | Very High | Academic rules complexity | Test-Driven Development (TDD) for State Machines |
