# Staff Domain — Entity Relationship Diagram (SPR-006)

## Purpose

Visualize the 9 tables of the Staff & Workforce domain and their relationships,
including the tenant-isolation and one-active-employment constraints that make
the domain provably correct.

---

## Diagram

```mermaid
erDiagram
    STAFFS ||--o| EMPLOYMENTS : "has one active contract"
    STAFFS ||--o{ EMPLOYMENT_HISTORY : "traces via employment"
    EMPLOYMENTS ||--o{ EMPLOYMENT_HISTORY : "append-only journal"
    STAFFS ||--o{ ASSIGNMENTS : "time-aware assignments"
    ASSIGNMENTS }o--o| DEPARTMENTS : "department assignment (nullable)"
    ASSIGNMENTS }o--o| POSITIONS : "position assignment (nullable)"
    STAFFS ||--o{ QUALIFICATIONS : "credentials"
    STAFFS ||--o{ CERTIFICATIONS : "certifications"
    STAFFS ||--o{ STAFF_EMERGENCY_CONTACTS : "emergency contacts"
    STAFFS ||--o| TEACHER_PROFILES : "teacher-only profile"
    STAFFS }o--o| USERS : "linked account (nullable)"
    POSITIONS }o--|| TENANTS : "school scoped"
    STAFFS }o--|| TENANTS : "school scoped"

    STAFFS {
        bigint id PK
        string uuid UK
        string school_id FK "36 chars"
        string staff_number UK "school-scoped unique"
        string first_name
        string last_name
        string specialization "StaffSpecialization enum"
        string status "StaffStatus enum"
        string email "school-scoped unique"
        string phone_number "school-scoped unique"
        string national_id "school-scoped unique"
        date date_of_birth
        bigint user_id FK "nullable"
        timestamp deleted_at "soft deletes"
    }

    EMPLOYMENTS {
        bigint id PK
        string school_id FK
        bigint staff_id FK
        string employment_type
        string contract_type
        string status "EmploymentStatus enum"
        string salary_scale "nullable"
        decimal salary_amount "nullable"
        date contract_start_date
        date contract_end_date "nullable"
        date termination_date "nullable"
        timestamp deleted_at "soft deletes"
    }

    EMPLOYMENT_HISTORY {
        bigint id PK
        string school_id FK
        bigint employment_id FK
        string event "event name"
        json payload
        bigint user_id "actor, nullable"
        timestamp created_at
    }

    ASSIGNMENTS {
        bigint id PK
        string school_id FK
        bigint staff_id FK
        bigint employment_id FK "nullable"
        string assignment_type "department|position|campus"
        bigint department_id FK "nullable"
        bigint position_id FK "nullable"
        string campus_school_id "nullable"
        date assignment_start_date
        date assignment_end_date "nullable"
        boolean is_current
        timestamp deleted_at "soft deletes"
    }

    POSITIONS {
        bigint id PK
        string school_id FK
        string title
        string category "PositionCategory enum"
        timestamp deleted_at "soft deletes"
    }

    QUALIFICATIONS {
        bigint id PK
        string school_id FK
        bigint staff_id FK
        string qualification_type "QualificationType enum"
        string institution
        string title
        date obtained_at
    }

    CERTIFICATIONS {
        bigint id PK
        string school_id FK
        bigint staff_id FK
        string name
        string status "CertificationStatus enum"
        date issued_at
        date expires_at "nullable"
    }

    STAFF_EMERGENCY_CONTACTS {
        bigint id PK
        string school_id FK
        bigint staff_id FK
        string full_name
        string relationship
        string phone_number
        boolean is_primary
    }

    TEACHER_PROFILES {
        bigint id PK
        string school_id FK
        bigint staff_id FK "unique"
        json teaching_subject_ids
        string teaching_levels "nullable"
    }
```

---

## Key Constraints

| Constraint | Location | Meaning |
|------------|----------|---------|
| `UNIQUE (school_id, staff_id) WHERE termination_date IS NULL` | `employments` | exactly one active contract per staff per school |
| `UNIQUE (school_id, staff_number)` | `staffs` | staff numbers never re-used (soft-delete only) |
| school-scoped uniqueness | `staffs` (email, phone, national_id) | enforced by actions |
| `school_id` FK → `tenants` on every table | all 9 tables | tenant isolation at database level |
| append-only | `employment_history` | events are inserted, never updated |
| `is_current` | `assignments` | time-aware; actions reject overlaps |

---

## Related Documents

- SPR-006 Staff Engine Report
- [ADR-006](../ADR/ADR-006.md)
- [ERD](ERD.md)
- [Relationships](Relationships.md)
- [MultiTenancy](MultiTenancy.md)
