# Academic Framework

**Document Version:** 1.0.0

**Status:** Approved

**Project:** NexusSchool

**Module:** Academic Framework Engine

**Last Updated:** 2026-06-27

---

# 1. Purpose

The Academic Framework defines the organizational structure of an educational institution.

All academic operations depend on this framework.

This module must be completed before implementing Students, Teachers, Attendance, Grades, Timetables or Examinations.

---

# 2. Architecture

```
School
│
├── Academic Calendar
│
├── Academic Year
│
├── Academic Period
│
├── Education Level
│
├── Program
│
├── Department
│
├── Class
│
├── Section
│
├── Subject
│
├── Teacher
│
└── Student
```

---

# 3. Academic Calendar

Defines how the institution organizes its school year.

Examples:

* Trimester
* Semester
* Quarter
* Module
* Custom

---

# 4. Academic Year

Represents one complete school year.

Example:

2025-2026

Only one Academic Year may be Active for a school.

---

# 5. Academic Period

Represents subdivisions of an Academic Year.

Examples:

General Education

* First Trimester
* Second Trimester
* Third Trimester

Technical Education

* Semester 1
* Semester 2

The number of periods is configurable.

---

# 6. Education Level

Represents the educational level.

Examples:

* Preschool
* Primary
* Junior Secondary
* Senior Secondary
* Technical
* Vocational
* University

---

# 7. Program

Represents the educational program.

Examples:

* General Education
* Technical Education
* Commercial
* Industrial
* Scientific
* Literary

---

# 8. Department

Represents academic departments.

Examples:

* Science
* Arts
* ICT
* Business

---

# 9. Class

Represents the official class.

Examples:

* 6ème
* 5ème
* 4ème
* 3ème
* Seconde C
* Première D
* Terminale A

---

# 10. Section

A Class may contain multiple Sections.

Examples:

A

B

C

D

---

# 11. Subject

Subjects belong to Classes and Programs.

Examples:

Mathematics

Physics

French

English

Computer Science

---

# 12. Teacher

Every Teacher belongs to:

* School
* Academic Year
* Department

A Teacher may teach multiple Subjects.

Each Teacher receives a unique Teacher Number.

Examples:

TEC-2026-00001

TCH-000254

The Teacher Number is a business identifier and must remain unique.

---

# 13. Student

Every Student belongs to:

* School
* Academic Year
* Program
* Department
* Class
* Section

Each Student receives a unique Student Number.

Examples:

STD-2026-000001

GBS-STD-00542

The Student Number is a business identifier and must remain unique.

---

# 14. Business Identifiers

Every important entity should have two identifiers.

Technical Identifier

* id
* uuid

Business Identifier

* student_number
* teacher_number
* employee_number
* guardian_number
* admission_number
* receipt_number
* invoice_number

Business identifiers are generated automatically by the Numbering Engine.

---

# 15. Future Module

## Identity & Numbering Engine

Responsible for generating all official business numbers.

Supported numbering:

* Student Number
* Teacher Number
* Employee Number
* Admission Number
* Invoice Number
* Receipt Number
* Library Card Number
* Hostel Number

The numbering format must be configurable by each school.

---

# 16. Design Principles

* Multi-school ready
* Multi-country ready
* Configurable academic calendars
* Flexible numbering system
* Historical data preservation
* No hardcoded academic structure
* Business identifiers separated from database identifiers

---

# Change History

| Version | Date       | Description                |
| ------- | ---------- | -------------------------- |
| 1.0.0   | 2026-06-27 | Initial Academic Framework |
