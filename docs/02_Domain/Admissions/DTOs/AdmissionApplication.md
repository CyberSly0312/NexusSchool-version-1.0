# Admission Application DTO

## Purpose

Transfers Admission Application data between layers.

---

## Properties

- id
- applicationNumber
- applicantId
- admissionCycleId
- academicProgramId
- academicLevelId
- status
- submittedAt
- remarks
- createdAt
- updatedAt

---

## Validation

- Applicant ID required.
- Admission Cycle required.
- Academic Program required.

---

## Used By

- Controllers
- Services
- APIs
- Jobs