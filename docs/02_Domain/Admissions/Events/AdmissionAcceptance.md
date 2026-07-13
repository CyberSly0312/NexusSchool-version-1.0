# Admission Acceptance Events

## Purpose

Defines domain events related to Admission Acceptances.

---

# Events

## AdmissionAccepted

### Trigger

Applicant accepts Admission.

### Consumers

- Enrollment Module
- Student Module

---

## AdmissionDeclined

### Trigger

Applicant declines Admission.

### Consumers

- Reporting Service

---

## StudentCreationStarted

### Trigger

Student creation begins.

### Consumers

- Student Service

---

## EnrollmentInitiated

### Trigger

Enrollment process starts.

### Consumers

- Enrollment Module
- Finance Module

---

## AdmissionAcceptanceCompleted

### Trigger

Admission Acceptance completed successfully.

### Consumers

- Audit Service