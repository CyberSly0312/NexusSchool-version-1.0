# Admission Exam Events

## Purpose

Defines domain events related to Admission Examinations.

---

# Events

## AdmissionExamScheduled

### Trigger

An Admission Examination is scheduled.

### Payload

- Examination ID
- Application ID
- Examination Session ID
- Scheduled Date
- Timestamp

### Consumers

- Calendar Service
- Notification Service

---

## AdmissionExamStarted

### Trigger

The Examination begins.

### Consumers

- Audit Service

---

## AdmissionExamCompleted

### Trigger

The Examination is completed.

### Consumers

- Grading Service
- Admission Decision Service

---

## AdmissionExamGraded

### Trigger

The Examination has been graded.

### Consumers

- Admission Decision Service

---

## AdmissionExamResultPublished

### Trigger

Results are published.

### Consumers

- Applicant Portal
- Notification Service