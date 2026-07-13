# Curriculum Engine

## Purpose

Manages curriculum structures and learning pathways.

---

## Responsibilities

- Manage curriculum
- Assign Subjects
- Configure credits
- Validate prerequisites
- Track curriculum versions

---

## Inputs

- Academic Program
- Subjects
- Credit Policies
- Curriculum Rules

---

## Outputs

- Curriculum Structure
- Subject Mapping
- Credit Distribution

---

## Business Rules

- Every Program must have one Curriculum.
- Subject prerequisites respected.
- Credit limits enforced.
- Curriculum versions immutable after publication.

---

## Processing Flow

1. Load Program
2. Load Subjects
3. Validate prerequisites
4. Build Curriculum
5. Publish Curriculum

---

## Produced Events

- CurriculumCreated
- CurriculumUpdated
- CurriculumPublished

---

## Security

- Version control
- Audit history

---

## Future Improvements

- Outcome-based curriculum
- AI curriculum optimization