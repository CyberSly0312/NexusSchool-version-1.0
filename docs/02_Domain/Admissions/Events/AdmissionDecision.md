# Admission Decision Events

## Purpose

Defines domain events related to Admission Decisions.

---

# Events

## AdmissionDecisionEvaluated

### Trigger

Application evaluation completed.

### Consumers

- Audit Service

---

## AdmissionDecisionApproved

### Trigger

Decision approved.

### Consumers

- Admission Offer Service
- Notification Service

---

## AdmissionDecisionRejected

### Trigger

Decision rejected.

### Consumers

- Notification Service

---

## AdmissionDecisionWaitlisted

### Trigger

Applicant added to waiting list.

### Consumers

- Waiting List Service

---

## AdmissionDecisionPublished

### Trigger

Decision published.

### Consumers

- Applicant Portal
- Reporting Service