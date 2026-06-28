# NexusSchool ERD V1

```mermaid
erDiagram

    SCHOOL ||--o{ ACADEMIC_YEAR : owns

    ACADEMIC_YEAR ||--o{ ACADEMIC_PERIOD : contains

    SCHOOL ||--o{ EDUCATION_LEVEL : has

    EDUCATION_LEVEL ||--o{ PROGRAM : contains

    PROGRAM ||--o{ DEPARTMENT : includes

    DEPARTMENT ||--o{ CLASSROOM : manages

    CLASSROOM ||--o{ SECTION : contains

    CLASSROOM ||--o{ SUBJECT : teaches

    SCHOOL ||--o{ TEACHER : employs

    SCHOOL ||--o{ STUDENT : enrolls
```