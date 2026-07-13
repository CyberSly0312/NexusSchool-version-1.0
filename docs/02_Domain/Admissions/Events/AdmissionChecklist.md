# Admission Checklist Events

## Purpose

Defines domain events related to Admission Checklists.

---

# Events

## AdmissionChecklistCreated

### Trigger

Checklist generated.

### Consumers

- Admission Officer Dashboard

---

## AdmissionChecklistUpdated

### Trigger

Checklist modified.

### Consumers

- Audit Service

---

## AdmissionChecklistApproved

### Trigger

Checklist approved.

### Consumers

- Enrollment Service

---

## EnrollmentAuthorized

### Trigger

Enrollment authorization granted.

### Consumers

- Student Module
- Enrollment Module

---

## AdmissionChecklistArchived

### Trigger

Checklist archived.

### Consumers

- Audit Service