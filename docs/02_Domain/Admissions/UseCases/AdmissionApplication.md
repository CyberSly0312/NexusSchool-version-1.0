# Admission Application Use Cases

## Purpose

Defines all business use cases related to Admission Applications.

---

# 1. Create Admission Application

## Actors

- Applicant
- Admission Officer

## Preconditions

- Admission Cycle is Open.
- Applicant account exists.

## Main Flow

1. Select Admission Cycle.
2. Select Academic Program.
3. Complete Application Form.
4. Save Draft.

## Postconditions

- Draft Application created.

---

# 2. Update Draft Application

## Actors

- Applicant

## Preconditions

- Application status is Draft.

## Main Flow

1. Open Draft.
2. Modify information.
3. Save changes.

## Postconditions

- Draft updated.

---

# 3. Submit Application

## Actors

- Applicant

## Preconditions

- Draft complete.
- Required Documents uploaded.

## Main Flow

1. Validate Application.
2. Submit.
3. Notify Admission Office.

## Postconditions

- Status becomes Submitted.

---

# 4. Withdraw Application

## Actors

- Applicant

## Preconditions

- Application not Approved.

## Main Flow

1. Request withdrawal.
2. Confirm action.

## Postconditions

- Status becomes Withdrawn.

---

# Related Services

- AdmissionApplicationService

# Related Events

- AdmissionApplicationCreated
- AdmissionApplicationSubmitted
- AdmissionApplicationWithdrawn