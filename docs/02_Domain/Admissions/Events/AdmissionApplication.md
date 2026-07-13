# Admission Application Events

## Purpose

Defines domain events related to Admission Applications.

---

# Events

## AdmissionApplicationCreated

### Trigger

Application created.

### Payload

- Application ID
- Applicant ID
- Admission Cycle ID
- Timestamp

### Consumers

- Audit Module
- Notification Module

---

## AdmissionApplicationSubmitted

### Trigger

Application submitted.

### Consumers

- Admission Review
- Document Verification

---

## AdmissionApplicationApproved

### Trigger

Application approved.

### Consumers

- Interview Module
- Examination Module
- Decision Module

---

## AdmissionApplicationRejected

### Trigger

Application rejected.

### Consumers

- Notification Module

---

## AdmissionApplicationArchived

### Trigger

Application archived.

### Consumers

- Audit Module