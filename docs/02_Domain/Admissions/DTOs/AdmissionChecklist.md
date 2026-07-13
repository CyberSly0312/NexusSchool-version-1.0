# Admission Checklist DTO

## Purpose

Transfers Admission Checklist information.

---

## Properties

- id
- applicationId
- checklistTemplateId
- completedItems
- pendingItems
- approvedBy
- approvedAt
- status
- createdAt
- updatedAt

---

## Validation

- Application required.
- Checklist Template required.
- Responsible Officer required.

---

## Used By

- AdmissionChecklistService
- Controllers
- Enrollment Module