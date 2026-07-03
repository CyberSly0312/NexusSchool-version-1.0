# Nexus School

# Academic Domain Business Rules

---

Document ID: NS-DOM-ACA-003

Version: 1.0.0

Status: Approved

Owner: Nexus Technologies

Author: Nexus School Engineering Team

Last Updated: 2026-06-28

Review Cycle: Major Releases Only

---

# Purpose

This document defines the business rules governing the Academic Domain.

Business rules describe the constraints, validations, and operational logic that must always be respected, regardless of the user interface or API implementation.

These rules are enforced by the application services and database constraints where applicable.

---

# Guiding Principles

The Academic Domain must ensure:

- Consistency
- Data integrity
- Predictability
- Configurability
- Multi-tenant isolation
- Traceability

---

# Academic Year Rules

## AY-001

Each tenant must have at least one Academic Year.

---

## AY-002

Only one Academic Year may have the status **Active** at any given time.

---

## AY-003

Academic Years must not overlap.

---

## AY-004

A closed Academic Year becomes read-only.

---

## AY-005

Only authorized administrators may create, modify, open, or close an Academic Year.

---

## AY-006

An Academic Year cannot be deleted if it contains academic records.

---

# Academic Term Rules

## AT-001

Each Academic Term belongs to exactly one Academic Year.

---

## AT-002

Terms within the same Academic Year must not overlap.

---

## AT-003

Only one Academic Term may be active at a time.

---

## AT-004

A closed term cannot receive new attendance, examinations, or grades.

---

# Education Level Rules

## EL-001

Every Class belongs to one Education Level.

---

## EL-002

Education Levels are configurable by each tenant.

---

## EL-003

Education Levels cannot be removed while still referenced.

---

# Department Rules

## DEP-001

Departments are optional.

Primary schools may operate without departments.

---

## DEP-002

Programs belong to Departments where applicable.

---

# Program Rules

## PRO-001

Programs may only exist within one Department.

---

## PRO-002

Programs may define their own curriculum.

---

## PRO-003

Programs cannot be deleted while students are enrolled.

---

# Class Rules

## CLS-001

Each Class belongs to one Academic Year.

---

## CLS-002

Each Class belongs to one Education Level.

---

## CLS-003

Each Class has a configurable maximum capacity.

---

## CLS-004

Student enrollment must not exceed class capacity unless an override permission is granted.

---

## CLS-005

Class names must be unique within the same Academic Year.

---

## CLS-006

Inactive Classes cannot receive new enrollments.

---

# Section Rules

## SEC-001

Sections belong to exactly one Class.

---

## SEC-002

Section names must be unique within the same Class.

---

# Subject Rules

## SUB-001

Every Subject has a unique code.

---

## SUB-002

Subjects may belong to multiple Programs.

---

## SUB-003

Subjects may be mandatory or optional.

---

## SUB-004

Subjects may define prerequisite subjects.

---

# Curriculum Rules

## CUR-001

Each Program may define one or more Curricula.

---

## CUR-002

Curricula determine:

- Subjects
- Credit Hours
- Assessment Rules
- Graduation Requirements

---

## CUR-003

Historical Curricula must remain available for previous Academic Years.

---

# Timetable Rules

## TT-001

A Teacher cannot teach two Classes simultaneously.

---

## TT-002

A Classroom cannot host multiple Classes simultaneously.

---

## TT-003

A Student cannot attend overlapping lessons.

---

## TT-004

Time conflicts must be prevented automatically.

---

# Classroom Rules

## ROOM-001

Each Classroom has a defined capacity.

---

## ROOM-002

Laboratories and specialized rooms may restrict Subject assignments.

---

# General Rules

## GEN-001

Every academic entity belongs to one Tenant.

---

## GEN-002

Cross-tenant data access is prohibited.

---

## GEN-003

All significant modifications must be audited.

---

## GEN-004

Soft Deletes must be used whenever historical information is important.

---

## GEN-005

UUIDs are used as public identifiers.

---

# Validation Rules

The system validates:

- Duplicate names
- Duplicate codes
- Invalid dates
- Overlapping schedules
- Missing relationships
- Invalid capacities
- Circular references

before committing changes.

---

# Exception Rules

Certain actions require elevated permissions, including:

- Overriding class capacity
- Reopening a closed Academic Year
- Editing historical records
- Manual timetable overrides

---

# Future Business Rules

Future versions may include:

- Cross-campus scheduling
- International academic calendars
- AI-assisted conflict detection
- Automatic curriculum recommendations

---

# Summary

These Business Rules ensure that all academic operations remain valid, consistent, and compliant with the educational structure defined by Nexus School.

---

# Conclusion

Every service, policy, workflow, API endpoint, and database constraint within the Academic Domain must enforce these rules to guarantee long-term data integrity and operational consistency.

---

**End of Document**