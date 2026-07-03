
# Table: academic_terms

## Purpose

Stores the academic periods within an Academic Year.

An Academic Year may contain one or more Terms depending on the educational system adopted by the school.

Examples:

- First Term
- Second Term
- Third Term

or

- Semester 1
- Semester 2

This design allows Nexus School to support different educational systems across countries.

---

## Table Name

academic_terms

---

## Primary Keys

- id (BIGINT)
- uuid (UUID)

---

## Columns

| Column | SQL Type | Nullable | Default | Index | Description |
|----------|----------|----------|----------|----------|----------------|
| id | BIGINT | No | Auto | PK | Internal identifier |
| uuid | UUID | No | Generated | UNIQUE | Public identifier |
| school_id | BIGINT | No | - | INDEX | Owner school |
| academic_year_id | BIGINT | No | - | INDEX | Parent Academic Year |
| name | VARCHAR(100) | No | - | INDEX | Full name |
| short_name | VARCHAR(20) | Yes | NULL | - | Short label |
| position | TINYINT | No | 1 | INDEX | Display order |
| start_date | DATE | No | - | - | Start date |
| end_date | DATE | No | - | - | End date |
| status | ENUM | No | upcoming | INDEX | Current status |
| is_exam_period | BOOLEAN | No | FALSE | - | Indicates whether examinations are scheduled |
| description | TEXT | Yes | NULL | - | Optional notes |
| created_at | TIMESTAMP | No | CURRENT_TIMESTAMP | - | Creation timestamp |
| updated_at | TIMESTAMP | No | CURRENT_TIMESTAMP | - | Last update |
| deleted_at | TIMESTAMP | Yes | NULL | INDEX | Soft delete |

---

## Status Values

- upcoming
- active
- completed
- archived

---

## Relationships

### belongsTo

AcademicYear

School

### hasMany (Future)

Examinations

Attendance Periods

Report Cards

Timetables

Academic Events

---

## Business Rules

- Every Term belongs to exactly one Academic Year.
- Every Term belongs to exactly one School.
- Terms belonging to the same Academic Year must not overlap.
- Start Date must be before End Date.
- Position must be unique within an Academic Year.
- Only one Term may have the status **active** inside an Academic Year.
- Archived Terms cannot be modified.

---

## Validation Rules

school_id

required

exists:schools,id

academic_year_id

required

exists:academic_years,id

name

required

string

max:100

short_name

nullable

string

max:20

position

required

integer

min:1

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

## Example Records

| Name | Position | Status |
|------|----------|---------|
| First Term | 1 | Completed |
| Second Term | 2 | Active |
| Third Term | 3 | Upcoming |

---

## Indexes

- school_id
- academic_year_id
- status
- position

Composite Index

(academic_year_id, position)

---

## Future Extensions

- Lock grading period
- Attendance closing date
- Examination window
- Report publication date
- National examination period
- Holiday calendar integration

---

## Security Considerations

Only authorized academic administrators may:

- Create Terms
- Modify Terms
- Archive Terms

Teachers have read-only access.

Students and Parents may only view the currently active Term.

---

## API Exposure

Public UUID

Internal ID never exposed outside the backend.

---

## Definition of Done

The table is complete when:

- Terms support multiple educational systems.
- Date overlap validation is enforced.
- Position ordering is supported.
- Future examination modules can rely on this structure.