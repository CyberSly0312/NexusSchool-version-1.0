# Nexus School

# Academic Domain Database Design

**Document ID:** NS-DOM-ACA-002

**Version:** 1.0.0

**Owner:** Nexus Technologies

**Status:** Draft

---

# Purpose

This document defines the database architecture of the Academic Domain.

It describes the core entities, their relationships, constraints, indexing strategy, and tenant isolation rules required to support the academic operations of Nexus School.

The objective is to create a scalable, maintainable, and extensible data model capable of supporting educational institutions of different sizes and educational systems.

---

# Design Principles

The Academic database follows these principles:

- Normalized data model
- UUID primary keys
- Multi-tenant isolation
- Soft deletes
- Audit support
- Extensible relationships
- Referential integrity

---

# Core Entities

The Academic Domain contains the following core entities:

- AcademicYear
- AcademicTerm
- AcademicPeriod
- EducationLevel
- Department
- Program
- Curriculum
- Class
- Section
- Subject
- Timetable
- Classroom

Each entity has a specific business responsibility.

---

# Entity Relationships

```
Tenant

↓

Academic Year

↓

Academic Term

↓

Academic Period

↓

Education Level

↓

Department

↓

Program

↓

Class

↓

Section

↓

Subject

↓

Curriculum

↓

Timetable
```

---

# Shared Fields

Every table should contain:

- UUID
- Tenant ID
- Created By
- Updated By
- Created At
- Updated At
- Deleted At

These fields provide auditing and tenant isolation.

---

# Academic Year

Stores academic sessions.

Examples:

- 2025/2026
- 2026/2027

Relationships:

- hasMany Academic Terms
- hasMany Classes
- hasMany Timetables

---

# Academic Term

Represents terms or trimesters.

Examples:

- First Term
- Second Term
- Third Term

Relationships:

- belongsTo Academic Year

---

# Academic Period

Represents smaller instructional periods if required.

Examples:

- Semester
- Quarter
- Trimester

This entity is configurable.

---

# Education Level

Examples:

- Kindergarten
- Primary
- Junior High
- Senior High
- Undergraduate

Programs belong to education levels.

---

# Department

Examples:

- Science
- Arts
- Business
- Engineering

Departments may exist only in institutions where applicable.

---

# Program

Examples:

- Computer Science
- Accounting
- Nursing

Programs belong to departments.

---

# Class

Represents a teaching group.

Examples:

- Grade 5A
- Form 2B
- CS101

A class belongs to:

- Academic Year
- Education Level
- Program (optional)

---

# Section

Represents subdivisions within a class.

Examples:

- A
- B
- Morning
- Evening

Sections belong to Classes.

---

# Subject

Represents academic subjects.

Examples:

- Mathematics
- Physics
- Chemistry
- English

Subjects may belong to multiple programs.

---

# Curriculum

Defines which subjects are taught.

A curriculum contains:

- Subjects
- Credit Hours
- Assessment Rules

Curricula may vary between programs.

---

# Timetable

Stores scheduled teaching sessions.

Relationships:

- Teacher
- Subject
- Classroom
- Time Slot
- Class

---

# Classroom

Represents physical teaching rooms.

Examples:

- Lab 01
- Room A203
- Library Hall

---

# Indexing Strategy

Indexes should exist for:

- UUID
- Tenant ID
- Academic Year
- Class
- Subject
- Program

Composite indexes should optimize common queries.

---

# Constraints

The database must enforce:

- Unique academic year per tenant.
- No duplicate class names within the same academic year.
- No duplicate subject codes.
- Referential integrity.
- Valid foreign keys.

---

# Soft Deletes

Soft Deletes are enabled for all academic entities.

Historical academic records must remain recoverable.

---

# Audit

Every modification should generate an audit entry.

Audit logs include:

- User
- Timestamp
- Action
- Previous Values
- New Values

---

# Performance

Database optimization includes:

- Proper indexing
- Pagination
- Lazy loading where appropriate
- Eager loading for common relationships

---

# Multi-Tenant Isolation

Every query must automatically filter by:

Tenant ID

Cross-tenant joins are prohibited.

---

# Future Expansion

The schema should support:

- Multiple campuses
- International curricula
- Academic partnerships
- Exchange programs
- AI recommendations

---

# Summary

The Academic database schema provides the structural foundation required for every academic process within Nexus School.

---

# Conclusion

A well-designed database ensures data integrity, scalability, maintainability, and long-term adaptability while supporting diverse educational institutions.

---

**End of Document**