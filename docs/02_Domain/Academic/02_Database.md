# Academic Database Design

**Project:** Nexus School ERP

**Module:** Academic Engine

**Document:** Database Design

**Version:** DEV-02

---

# Overview

This document describes the complete database architecture of the Academic Engine.

Every table defined here belongs to exactly one school.

The Academic Engine is designed to support multiple schools, multiple academic years, and multiple educational systems.

---

# Database Conventions

## Primary Keys

Every table must contain:

```sql
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

uuid UUID UNIQUE
```

---

## Foreign Keys

Foreign keys use the Laravel convention.

Example:

school_id

academic_year_id

program_id

level_id

---

## Audit Columns

Every table includes:

created_at

updated_at

---

## Soft Deletes

Only business-critical tables should support soft deletes.

Examples:

Programs

Levels

Subjects

School Classes

Students

Teachers

Academic Years

---

# Table : academic_years

## Purpose

Represents one complete school year.

Only one Academic Year can be active per school.

---

## Columns

id

uuid

school_id

name

start_date

end_date

status

description

created_at

updated_at

deleted_at

---

## Status

active

closed

archived

---

## Constraints

One active Academic Year per School.

End date must be greater than Start date.

Academic Years cannot overlap.

---

## Relationships

School

↓

hasMany AcademicYears

AcademicYear

↓

belongsTo School

AcademicYear

↓

hasMany AcademicTerms

---

## Indexes

school_id

status

name

---

## Example

2026-2027

01/09/2026

30/06/2027

Status:

Active

---

# Table : academic_terms

## Purpose

Represents periods inside an Academic Year.

---

## Columns

id

uuid

school_id

academic_year_id

name

short_name

start_date

end_date

position

status

created_at

updated_at

deleted_at

---

## Status

Upcoming

Active

Closed

---

## Constraints

Terms cannot overlap.

Terms must belong to an Academic Year.

Position must be unique within the Academic Year.

---

## Relationships

AcademicYear

↓

hasMany AcademicTerms

AcademicTerm

↓

belongsTo AcademicYear

---

## Example

Term 1

Term 2

Term 3

---

# Table : programs

## Purpose

Represents a type of educational program.

Examples:

Junior High

Senior High

Technical

Vocational

---

## Columns

id

uuid

school_id

name

code

description

display_order

status

created_at

updated_at

deleted_at

---

## Status

Active

Inactive

---

## Relationships

School

↓

hasMany Programs

Program

↓

hasMany Levels

---

## Constraints

Program code must be unique per School.

Display order must be unique.

---

## Example

Name:

Senior High School

Code:

SHS

---

# Table : levels

## Purpose

Represents academic levels inside a Program.

---

## Columns

id

uuid

school_id

program_id

name

short_name

display_order

allow_series

description

status

created_at

updated_at

deleted_at

---

## Relationships

Program

↓

hasMany Levels

Level

↓

belongsTo Program

Level

↓

hasMany SchoolClasses

Level

↓

hasMany Series

---

## Constraints

Display order unique inside Program.

allow_series determines whether students must choose a Series.

---

## Example

Name

Terminale

Short Name

Tle

Allow Series

True

---

# Current Entity Relationship

School

│

├── Academic Years

│

├── Programs

│

└── Levels

Academic Years

↓

Academic Terms

Programs

↓

Levels