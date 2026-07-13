# Eligibility Engine

## Purpose

The Eligibility Engine determines whether an Applicant satisfies all admission requirements before entering the Admission process.

It evaluates institutional policies, academic prerequisites, age restrictions, nationality requirements, previous education, and program-specific conditions.

---

# Responsibilities

- Validate Applicant eligibility.
- Verify academic prerequisites.
- Validate age requirements.
- Validate admission requirements.
- Verify application deadlines.
- Produce eligibility decisions.

---

# Inputs

- Applicant Profile
- Academic Records
- Admission Requirements
- Academic Program
- Admission Cycle
- Applicant Nationality
- Date of Birth

---

# Outputs

- Eligibility Status
- Validation Report
- Failed Requirements
- Eligibility Score

---

# Dependencies

- Admission Requirement Service
- Academic Framework
- Program Service
- Admission Cycle Service

---

# Validation Rules

## Academic Qualification

Applicant must satisfy minimum academic requirements.

---

## Age Requirement

Applicant age must fall within the permitted range.

---

## Admission Cycle

Admission Cycle must be Open.

---

## Program Availability

Academic Program must accept new Applicants.

---

## Duplicate Application

Only one active Application is allowed.

---

# Processing Flow

1. Load Applicant Profile.
2. Load Program Requirements.
3. Validate Academic Records.
4. Validate Age.
5. Validate Admission Cycle.
6. Validate Duplicate Applications.
7. Produce Eligibility Result.

---

# Produced Events

- EligibilityValidated
- EligibilityFailed

---

# Consumed Events

- AdmissionApplicationCreated

---

# Failure Handling

- Missing Applicant Information
- Missing Academic Records
- Invalid Admission Cycle
- Requirement Validation Failure

---

# Performance

- Cache Admission Requirements.
- Parallel validation of independent rules.

---

# Security

- Tenant isolation.
- Audit every eligibility evaluation.
- Prevent unauthorized access.

---

# Future Improvements

- AI Eligibility Prediction.
- Country-specific admission policies.
- Automatic prerequisite recommendations.