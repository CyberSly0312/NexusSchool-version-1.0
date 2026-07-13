# Admission Exam DTO

## Purpose

Transfers Admission Examination data between application layers.

---

## Properties

- id
- applicationId
- examinationSessionId
- examinationDate
- examinerId
- score
- grade
- status
- remarks
- createdAt
- updatedAt

---

## Validation

- Application required.
- Examination Session required.
- Examiner required.
- Examination Date required.

---

## Used By

- AdmissionExamService
- Controllers
- APIs
- Examination Module