# Nexus School

# Academic Domain Entity Relationship Diagram (ERD)

---

**Document ID:** NS-DOM-ACA-009

**Version:** 1.0.0

**Status:** Approved

**Owner:** Nexus Technologies

**Author:** Nexus School Engineering Team

**Review Cycle:** Major Releases

---

# Purpose

This document defines the conceptual Entity Relationship Model (ERD) for the Academic Domain.

It describes the major business entities, their relationships, and the cardinality between them. This document serves as the foundation for the database schema and Eloquent model relationships.

---

# Objectives

The ERD aims to:

- Define the academic data model.
- Ensure data consistency.
- Support business rules.
- Guide database implementation.
- Simplify future maintenance.
- Support multi-tenant architecture.

---

# Core Academic Entities

The Academic Domain includes the following entities:

- Tenant
- AcademicYear
- AcademicTerm
- AcademicPeriod
- EducationLevel
- Department
- Program
- SchoolClass
- Section
- Subject
- Curriculum
- Classroom
- Timetable
- TimetableEntry

---

# High-Level Relationships

```
Tenant
│
├── AcademicYears
│     ├── AcademicTerms
│     │      └── AcademicPeriods
│     │
│     ├── SchoolClasses
│     │      ├── Sections
│     │      └── Timetables
│     │
│     └── Curriculum
│
├── Departments
│      └── Programs
│              └── Subjects
│
└── EducationLevels
       └── SchoolClasses
```

---

# Relationship Definitions

## Tenant

Tenant has many:

- Academic Years
- Departments
- Education Levels
- School Classes
- Subjects
- Classrooms

---

## AcademicYear

AcademicYear belongs to:

- Tenant

AcademicYear has many:

- Academic Terms
- School Classes
- Timetables

---

## AcademicTerm

AcademicTerm belongs to:

- AcademicYear

AcademicTerm has many:

- AcademicPeriods

---

## AcademicPeriod

AcademicPeriod belongs to:

- AcademicTerm

---

## EducationLevel

EducationLevel belongs to:

- Tenant

EducationLevel has many:

- SchoolClasses

---

## Department

Department belongs to:

- Tenant

Department has many:

- Programs

---

## Program

Program belongs to:

- Department

Program has many:

- Subjects
- Curricula

---

## Subject

Subject belongs to many:

- Programs

Subject belongs to many:

- SchoolClasses

---

## SchoolClass

SchoolClass belongs to:

- AcademicYear
- EducationLevel

SchoolClass has many:

- Sections
- Subjects
- Timetables

---

## Section

Section belongs to:

- SchoolClass

---

## Curriculum

Curriculum belongs to:

- Program

Curriculum contains:

- Subjects

---

## Classroom

Classroom belongs to:

- Tenant

Classroom has many:

- Timetable Entries

---

## Timetable

Timetable belongs to:

- SchoolClass

Timetable has many:

- Timetable Entries

---

## Timetable Entry

TimetableEntry belongs to:

- Timetable
- Subject
- Teacher
- Classroom

---

# Cardinality Summary

| Relationship | Cardinality |
|--------------|-------------|
| Tenant → AcademicYear | 1:N |
| AcademicYear → AcademicTerm | 1:N |
| AcademicTerm → AcademicPeriod | 1:N |
| Tenant → Department | 1:N |
| Department → Program | 1:N |
| Program → Subject | N:M |
| EducationLevel → SchoolClass | 1:N |
| AcademicYear → SchoolClass | 1:N |
| SchoolClass → Section | 1:N |
| SchoolClass → Subject | N:M |
| Program → Curriculum | 1:N |
| Curriculum → Subject | N:M |
| SchoolClass → Timetable | 1:N |
| Timetable → TimetableEntry | 1:N |
| Classroom → TimetableEntry | 1:N |

---

# Multi-Tenant Rules

Every entity within the Academic Domain is associated with exactly one Tenant.

Cross-tenant relationships are prohibited.

All queries must automatically scope data to the current tenant.

---

# Referential Integrity

The database must enforce:

- Foreign Key Constraints
- Cascade Rules where appropriate
- Soft Deletes
- UUID Primary Keys
- Audit Columns

---

# Future Relationships

Future versions may introduce relationships with:

- Student Domain
- Teacher Domain
- Examination Domain
- Attendance Domain
- Finance Domain
- Library Domain
- Hostel Domain
- Transport Domain
- AI Services

---

# Related Documents

- 01_Overview.md
- 02_Database.md
- 03_Business_Rules.md
- 07_API.md

---

# Summary

The Academic ERD provides a conceptual view of the Academic Domain and defines the relationships that will be implemented in the database and application models.

---

# Conclusion

This Entity Relationship Diagram serves as the authoritative reference for database design, Eloquent relationships, and future domain integrations.

---

**End of Document**