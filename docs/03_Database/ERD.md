# Entity Relationship Diagram (ERD)

## Purpose

The Entity Relationship Diagram (ERD) provides a high-level visualization of the relationships between the major business entities within Nexus School.

---

## Objectives

- Visualize database structure.
- Understand entity relationships.
- Support database design.
- Facilitate maintenance.

---

## Core Domains

### Academic

- Academic Years
- Academic Terms
- Education Levels
- Programs
- Departments
- Subjects
- Classes
- Sections

---

### Admissions

- Applicants
- Applications
- Documents
- Interviews
- Entrance Exams
- Admission Offers

---

### Students

- Students
- Parents
- Enrollments
- Student Status

---

### Teaching

- Teachers
- Timetables
- Attendance
- Assessments
- Grades

---

### Finance

- Fee Structures
- Invoices
- Payments
- Scholarships
- Refunds

---

### Campus Services

- Library
- Hostel
- Transport
- Canteen

---

## Relationship Principles

- One-to-One
- One-to-Many
- Many-to-Many
- Polymorphic Relations

---

## Diagram Strategy

Large ERDs should be divided into domain-specific diagrams:

- Academic ERD
- Admissions ERD
- Students ERD
- Finance ERD
- HR ERD
- Library ERD

---

## Documentation

ERDs must remain synchronized with database migrations and domain documentation.