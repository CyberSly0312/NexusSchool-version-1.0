# NexusSchool Enterprise Platform

## Dependency Map

**Document ID:** GOV-003

**Version:** 1.0.0

**Status:** Active

**Owner:** NexusSchool Architecture Team

**Last Updated:** 05 August 2026

---

# 1. Purpose

This document defines the dependency graph of the NexusSchool platform.

It specifies which Domains consume other Domains, the direction of dependencies, and the architectural constraints governing inter-domain communication.

Dependencies defined in this document are mandatory.

Circular dependencies are strictly prohibited.

---

# 2. Platform Dependency Rules

The following rules apply to every Domain.

* Dependencies flow in one direction only.
* No circular dependency is permitted.
* Domains communicate only through Contracts or Domain Events.
* Infrastructure is never consumed directly by another Domain.
* Business logic cannot be duplicated.
* Every dependency must have a documented purpose.

---

# 3. Foundation Layer

The Foundation Layer provides shared platform capabilities.

```
Foundation
│
├── Authentication
├── Authorization
├── Tenancy
├── Settings
├── Audit
├── Notifications
├── Permissions
└── Shared Kernel
```

Every Domain may consume the Foundation Layer.

The Foundation Layer consumes no Business Domain.

---

# 4. Academic Dependency Flow

```
Academic Framework
        │
        ▼
Student
        │
        ▼
Enrollment
        │
        ▼
Attendance
        │
        ▼
Assessment
        │
        ▼
Academic Policies
        │
        ▼
Gradebook
        │
        ▼
Class Council
        │
        ▼
Academic Communication
        │
        ▼
Report Card
        │
        ▼
Records Lifecycle Management
        │
        ▼
Certificate & Transcript
        │
        ▼
Identity & Verification
```

This represents the official academic processing pipeline.

---

# 5. Student Lifecycle

```
Admission

↓

Enrollment

↓

Class Assignment

↓

Attendance

↓

Assessment

↓

Gradebook

↓

Class Council

↓

Academic Decision

↓

Publication

↓

Report Card

↓

Certificate

↓

Archive

↓

Alumni
```

Every learner follows this lifecycle.

---

# 6. Staff Dependency

```
Staff

↓

Subject Assignment

↓

Timetable

↓

Attendance

↓

Assessment

↓

Class Council
```

Staff data must never depend on Gradebook or Report Card.

---

# 7. Guardian Dependency

```
Guardian

↓

Student

↓

Communication

↓

Parent Portal
```

Guardians consume information only.

They never produce academic decisions.

---

# 8. Communication Pipeline

```
Business Domain

↓

Academic Communication Engine

↓

Rendering

↓

Publication

↓

Distribution

↓

Portal

↓

Archive
```

Business Domains never publish directly.

---

# 9. Report Card Pipeline

```
Assessment

↓

Gradebook

↓

Academic Policies

↓

Class Council

↓

Academic Communication

↓

Report Card

↓

Archive
```

The Report Card Engine never performs calculations.

---

# 10. Records Lifecycle

```
Publication

↓

Active Record

↓

Read Only

↓

Archive

↓

Cold Storage

↓

Retention Policy

↓

Permanent Preservation

or

Secure Destruction
```

Records are never removed without an approved retention policy.

---

# 11. Portal Dependencies

```
Parent Portal

↓

Academic Communication

↓

Archive

↓

Notification
```

```
Student Portal

↓

Academic Communication

↓

Report Card

↓

Certificates

↓

Archive
```

```
Teacher Portal

↓

Assessment

↓

Attendance

↓

Timetable

↓

Communication
```

```
Secretary Portal

↓

Enrollment

↓

Communication

↓

Certificates

↓

Archive
```

```
Director Portal

↓

Everything
```

The Director Portal is the only operational portal with complete visibility, subject to tenant boundaries.

---

# 12. Finance Dependencies

```
Enrollment

↓

Billing

↓

Payment

↓

Receipt

↓

Communication

↓

Archive
```

Financial documents follow the same publication lifecycle as academic documents.

---

# 13. Archive Dependencies

The Records Lifecycle Management Engine receives documents from:

* Academic Communication
* Finance
* Human Resources
* Library
* Inventory
* Administration

It never creates business data.

---

# 14. Future Platform Services

Future services shall consume existing Contracts.

Examples include:

* AI Assistant
* Learning Management System (LMS)
* Computer-Based Testing (CBT)
* Biometric Attendance
* RFID Integration
* School Bus Tracking
* Mobile Applications
* Government Reporting
* External APIs

No future service may access Domain internals directly.

---

# 15. Forbidden Dependencies

The following dependencies are prohibited.

* Report Card → Assessment
* Communication → Assessment
* Communication → Gradebook calculations
* Archive → Business logic
* Certificates → Grade calculations
* Portals → Database models of other Domains
* Finance → Student internal implementation
* Any Domain → Infrastructure of another Domain

Violations are considered architectural defects.

---

# 16. Dependency Validation

Every new sprint must answer the following questions before implementation.

* Does this introduce a new dependency?
* Is the dependency one-directional?
* Is there already an existing Contract?
* Does this create duplication?
* Does it violate Domain ownership?
* Does it increase coupling?

---

# 17. Change Management

Any modification to the dependency graph requires:

* Architecture review
* Updated Dependency Map
* Updated Architecture Decision Record (ADR)
* Regression testing

---

# 18. Approval

This document defines the official dependency graph of the NexusSchool platform.

No implementation may violate the dependency hierarchy defined herein.
