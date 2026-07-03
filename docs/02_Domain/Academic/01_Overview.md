# Academic Engine Overview

**Project:** Nexus School ERP

**Module:** Academic Engine

**Version:** DEV-02

**Status:** Draft

**Last Updated:** June 2026

---

# 1. Purpose

The Academic Engine is the core domain of Nexus School.

Every academic module depends on it.

Without an academic structure, it is impossible to manage:

- Students
- Teachers
- Timetables
- Attendance
- Examinations
- Report Cards
- Promotions
- Graduations

This module defines the academic hierarchy used by the entire ERP.

---

# 2. Objectives

The Academic Engine must provide a flexible and configurable academic structure that supports:

- Secondary Schools
- High Schools
- Technical Schools
- Vocational Institutions

Future versions will also support:

- Universities
- Primary Schools

---

# 3. Design Principles

The Academic Engine must be:

- Modular
- Configurable
- Country-independent
- Multi-school ready
- Multi-academic-year ready
- Scalable
- Maintainable

Every academic entity belongs to one school.

No academic data should exist outside a school.

---

# 4. Core Concepts

The Academic Engine is composed of the following entities.

## Academic Year

Represents one complete school year.

Examples:

- 2025–2026
- 2026–2027

Only one Academic Year can be active per school.

---

## Academic Term

Represents a subdivision of an Academic Year.

Examples:

- First Term
- Second Term
- Third Term

or

- Semester 1
- Semester 2

The number of terms must be configurable.

---

## Program

Represents an educational program.

Examples:

- Junior High School
- Senior High School
- Technical Education
- Vocational Education

Programs organize Levels.

---

## Level

Represents an educational level.

Examples:

- Form 1
- Form 2
- Form 3

or

- 6ème
- 5ème
- 4ème
- 3ème
- Seconde
- Première
- Terminale

Levels belong to Programs.

---

## Series

Represents a specialization.

Examples:

- A
- C
- D
- F1
- F2
- G1

Series are optional.

Some Levels do not use Series.

---

## Class

Represents a teaching group.

Examples:

- Terminale D1
- 5ème B
- Form 2 Gold

Students belong to Classes.

Teachers teach Classes.

---

## Section

Represents subdivisions inside a Class.

Examples:

- Group A
- Laboratory Group
- Practical Group

Sections are optional.

---

## Subject

Represents an academic subject.

Examples:

- Mathematics
- English
- Physics
- Chemistry
- ICT

Subjects are assigned to Classes.

---

## Subject Coefficient

Represents the weight of a subject.

Different Classes may use different coefficients.

---

## Classroom

Represents a physical room.

Examples:

- Room A01
- Laboratory 2
- Computer Lab

Used by the Timetable module.

---

# 5. Academic Hierarchy

The Academic Engine follows this hierarchy:

School

↓

Academic Year

↓

Academic Term

↓

Program

↓

Level

↓

Series (Optional)

↓

Class

↓

Section (Optional)

↓

Students

Subjects are attached to Classes.

Teachers are assigned to Subjects.

Timetables are generated from these assignments.

---

# 6. Dependencies

This module is required before developing:

- Student Management
- Teacher Management
- Timetable
- Attendance
- Examination
- Report Cards
- Finance
- Parent Portal

---

# 7. Out of Scope

The following modules are NOT part of the Academic Engine:

- Student Admissions
- Teacher Profiles
- Attendance
- Timetable Generation
- Examination Results
- Finance
- Messaging

These modules consume the Academic Engine but do not belong to it.

---

# 8. Business Rules

- One school can have multiple Academic Years.
- Only one Academic Year may be active.
- One Academic Year contains multiple Terms.
- Programs belong to one School.
- Levels belong to one Program.
- Series are optional.
- Classes belong to one Level.
- Sections are optional.
- Subjects may belong to multiple Classes.
- Teachers may teach multiple Subjects.
- Students belong to one active Class.

---

# 9. Naming Conventions

Database

snake_case

Examples:

academic_years

academic_terms

subject_coefficients

teacher_assignments

---

Laravel Models

Singular

AcademicYear

AcademicTerm

Program

Level

Series

SchoolClass

Section

Subject

Classroom

---

Primary Keys

All tables use:

- id
- uuid

UUIDs are used for APIs.

---

# 10. Future Extensions

The Academic Engine has been designed to support future features without breaking the architecture.

Planned extensions include:

- Curriculum Management
- Learning Outcomes
- Competency-Based Assessment
- National Education Templates
- Multiple Campuses
- Online Classes
- AI Scheduling
- Cross-School Academic Reports

---

# Definition of Done

This document is complete when:

- All academic concepts are defined.
- The hierarchy is validated.
- Dependencies are identified.
- Naming conventions are approved.
- Future modules can safely rely on this architecture.