# Admission Exam Use Cases

## Purpose

Defines all business processes related to Admission Examinations.

---

# Supported Use Cases

- Schedule Examination
- Register Candidate
- Assign Examiner
- Conduct Examination
- Record Attendance
- Grade Examination
- Publish Results
- Cancel Examination

---

## Business Rules

- Only eligible Applicants may register.
- Every Examination requires an assigned Examiner.
- Published Results are immutable.
- Cancelled Examinations cannot be graded.

---

## Related Services

- AdmissionExamService

---

## Related Events

- AdmissionExamScheduled
- AdmissionExamStarted
- AdmissionExamCompleted
- AdmissionExamGraded
- AdmissionExamResultPublished

---

## Related Permissions

- admissions.exam.schedule
- admissions.exam.register
- admissions.exam.grade
- admissions.exam.publish