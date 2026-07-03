# Table: academic_years

## Purpose

Stores the academic years for a school.

An academic year represents one complete educational cycle.

Examples:

- 2025-2026
- 2026-2027

Each school can own multiple academic years.

Only one academic year can be active at a time.

---

## Table Name

academic_years

---

## Primary Key

id (BIGINT)

uuid (UUID)

---

## Columns

| Column | SQL Type | Nullable | Default | Index | Description |
|----------|----------|----------|----------|----------|----------------|
| id | BIGINT | No | Auto | PK | Internal identifier |
| uuid | UUID | No | Generated | UNIQUE | Public identifier |
| school_id | BIGINT | No | - | INDEX | Owner school |
| name | VARCHAR(100) | No | - | INDEX | Academic year name |
| start_date | DATE | No | - | - | Beginning of the academic year |
| end_date | DATE | No | - | - | End of the academic year |
| status | ENUM | No | active | INDEX | Current state |
| description | TEXT | Yes | NULL | - | Optional notes |
| created_at | TIMESTAMP | No | CURRENT_TIMESTAMP | - | Creation date |
| updated_at | TIMESTAMP | No | CURRENT_TIMESTAMP | - | Last update |
| deleted_at | TIMESTAMP | Yes | NULL | INDEX | Soft delete |

---

## Status Values

active

closed

archived

---

## Relationships

belongsTo

School

hasMany

AcademicTerms

---

## Business Rules

Only one Academic Year may be Active.

Academic Years cannot overlap.

End Date must be greater than Start Date.

Deleting an Academic Year is forbidden if:

- Classes exist
- Students exist
- Exams exist

Instead, archive it.

---

## Validation Rules

school_id

required

exists:schools,id

name

required

string

max:100

unique per school

start_date

required

date

end_date

required

date

after:start_date

status

required

enum

---

## Example Record

Name

2026-2027

Start Date

2026-09-15

End Date

2027-06-30

Status

Active

---

## Future Extensions

Academic Calendar

Public Holidays

National Exams

Special Events

Academic Weeks