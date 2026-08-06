# NexusSchool Enterprise Platform

# Session Handoff

**Document ID:** OPS-001

**Version:** 1.0.0

**Status:** Active

**Owner:** Architecture Team

**Last Updated:** 06 August 2026

---

# 1. Purpose

This document serves as the official handoff point between development sessions.

Its objective is to preserve complete project continuity regardless of:

* AI assistant used
* Developer involved
* Time elapsed between sessions
* Sprint currently in progress

Every development session SHALL begin by reviewing this document.

Every completed session SHALL update this document before ending.

---

# 2. Current Development Status

Platform

NexusSchool Enterprise Platform

Current Development Phase

Phase 2 — Academic Intelligence

Current Sprint

SPR-014 — Records Lifecycle Management

Current Status

🟡 Active Development

---

# 3. Current Sprint Objective

SPR-014 delivers the **Records Lifecycle Management Engine**: the permanent
academic record of every learner, with retention policies, an archive
lifecycle and long-term preservation.

SPR-013 (Class Council Engine) is **Complete**. Academic Intelligence (Phase 2)
is finished: Gradebook calculates, Class Council validates, Report Card
composes and Academic Communication publishes — all wired through contracts.

No Business Engine may override council decisions outside the Class Council
layer.

---

# 4. Recently Completed Work

The following major milestones have been completed.

## Platform Foundation

* Multi-Tenant Architecture
* Authentication
* Authorization
* Shared Kernel
* Architecture Governance

---

## Academic Foundation

* Academic Framework
* Student Domain
* Enrollment Domain
* Guardian Domain
* Staff Domain
* Timetable Engine
* Attendance Engine
* Assessment Engine
* Academic Policies Engine

---

## Academic Intelligence (Phase 2 — Complete)

* Gradebook Engine (SPR-010)
* Report Card Engine (SPR-011)
* Academic Communication Engine (SPR-012)
* Class Council Engine (SPR-013)

---

## Engineering Governance

* PHPStan + Larastan
* Pest Test Suite
* Laravel Pint
* Architecture Tests
* ADR Collection
* Governance Documentation

---

# 5. Work In Progress

Current implementation focuses on

* Records Lifecycle Management
* Permanent academic records
* Retention policies
* Archive lifecycle
* Cloud archive integration

---

# 6. Immediate Next Sprint

SPR-014

Records Lifecycle Management

Objectives

* Design the permanent academic record
* Define retention policies
* Introduce archive lifecycle
* Plan cloud archive integration

---

# 7. Upcoming Major Sprints

SPR-015

Certificate & Transcript Engine

SPR-016

Identity & Verification Engine

SPR-017

Archive & Cloud Storage Platform

---

# 8. Architectural Decisions Made

The following decisions are considered immutable.

* Academic rules belong exclusively to Academic Policies.
* Grade calculations belong exclusively to Gradebook.
* Academic validation belongs exclusively to Class Council.
* Business Engines never generate PDFs.
* Academic Communication Engine is the single publication layer.
* Published documents are immutable.
* Historical records are never modified.
* Corrections always create new versions.

---

# 9. Active Design Decisions

Current architectural discussions include

* Long-term archive lifecycle
* Cloud archive service
* Downloadable school archive packages
* Storage quotas
* Paid archive extensions
* Parent visibility policies
* Student visibility policies
* Publication approval workflow
* Academic council report publication
* Official meeting minutes publication

---

# 10. Pending Decisions

The following topics require future architectural review.

* Cloud object storage provider
* Email provider
* SMS provider
* Push notification provider
* Digital signature implementation
* Government verification services
* Public document verification portal

---

# 11. Files Modified During Current Sprint

## SPR-013 (Complete) — Class Council Engine

* app/Domains/ClassCouncil/*
* app/Domains/ReportCard/Infrastructure/Contracts/ClassCouncilAdapter.php
* app/Domains/AcademicCommunication/Infrastructure/Contracts/ClassCouncilAdapter.php
* config/nexus-class-council.php
* database/migrations/2026_08_06_000015..000017
* database/factories/ClassCouncil/*
* tests/Unit/Domains/ClassCouncil/*
* tests/Feature/Domains/ClassCouncil/*
* docs/ADR/ADR-013.md
* docs/00_Project_Management/SPR-013_Class_Council_Engine_Report.md
* phpstan-baseline.neon

## SPR-014 (In Progress)

This section shall be updated after every working session.

---

# 12. Quality Status

Current Quality Gates

✅ Pest

✅ PHPStan

✅ Larastan

✅ Laravel Pint

✅ Architecture Tests

✅ Contract Tests

✅ Tenant Isolation

---

# 13. Known Issues

Current legacy issues inherited from previous architecture remain outside the sprint scope.

No regression introduced by current work.

---

# 14. Session Resume Checklist

Before continuing development

* Read MASTER_ROADMAP.md
* Read CURRENT_POSITION.md
* Read PROJECT_STATE.md
* Read this Session Handoff
* Review current Sprint Report
* Review current ADRs
* Verify Quality Dashboard

---

# 15. Session Closing Checklist

Before ending a working session

* Update Sprint Report
* Update Current Position
* Update Project State
* Update Changelog
* Update this Session Handoff
* Run Pest
* Run PHPStan
* Run Laravel Pint
* Commit changes

---

# 16. OpenCode Prompt Continuity

Every implementation prompt shall include

* Current Sprint
* Sprint Scope
* Architectural Constraints
* Contracts to respect
* Quality Gates
* Documentation requirements

No implementation may continue without explicit sprint context.

---

# 17. Next Session Starting Point

Resume development from the current sprint.

Do not redesign completed architecture.

Do not duplicate existing business logic.

Respect all Architecture Decisions and Domain Contracts.

Continue implementation according to the Master Roadmap.

---

# 18. Final Statement

This document is the operational memory of the NexusSchool project.

Its purpose is to ensure that every development session begins with complete architectural awareness and ends with sufficient context for the next contributor to continue seamlessly.

Project continuity is considered a core engineering requirement of NexusSchool.
