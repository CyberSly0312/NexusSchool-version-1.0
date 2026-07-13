# Interview Engine

## Purpose

The Interview Engine manages the planning, execution, evaluation, and scoring of Admission Interviews.

It ensures that interviews are conducted consistently, fairly, and according to institutional policies.

---

# Responsibilities

- Schedule interviews.
- Assign interview panels.
- Record interview attendance.
- Collect interviewer evaluations.
- Calculate interview scores.
- Generate interview reports.

---

# Inputs

- Applicant Profile
- Admission Application
- Interview Schedule
- Interview Panel
- Evaluation Criteria
- Interview Scores

---

# Outputs

- Interview Status
- Evaluation Report
- Interview Score
- Interview Recommendation

---

# Dependencies

- AdmissionApplicationService
- Staff Service
- Calendar Service
- Notification Engine

---

# Evaluation Criteria

## Communication Skills

Measures verbal and written communication.

---

## Academic Motivation

Evaluates applicant motivation.

---

## Critical Thinking

Assesses reasoning ability.

---

## Personality

Evaluates behavior and professionalism.

---

## Program Suitability

Measures compatibility with the selected academic program.

---

# Processing Flow

1. Schedule interview.
2. Notify participants.
3. Register attendance.
4. Collect interviewer scores.
5. Calculate average score.
6. Produce recommendation.

---

# Produced Events

- InterviewScheduled
- InterviewStarted
- InterviewCompleted
- InterviewReviewed

---

# Consumed Events

- AdmissionApplicationApproved

---

# Failure Handling

- Applicant absent.
- Interviewer unavailable.
- Incomplete evaluation.
- Technical interruption.

---

# Performance

- Support concurrent interview sessions.
- Automatic interviewer assignment.

---

# Security

- Authorized interviewers only.
- Complete audit trail.
- Confidential interview notes.

---

# Future Improvements

- Online video interviews.
- AI-assisted interview evaluation.
- Automatic scheduling optimization.
- Speech sentiment analysis.