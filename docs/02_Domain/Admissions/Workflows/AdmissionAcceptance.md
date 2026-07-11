# Admission Acceptance Workflow

## Purpose

This workflow defines the process through which an Applicant accepts or declines an Admission Offer before becoming an enrolled Student.

---

# Trigger

Applicant receives an Admission Offer.

---

# Preconditions

- Admission Offer has been published.
- Offer has not expired.
- Applicant account is active.

---

# Workflow Steps

1. Applicant receives Admission Offer.
2. Applicant reviews Offer details.
3. Applicant accepts or declines the Offer.
4. System validates Offer expiration.
5. Acceptance is recorded.
6. Registration deposit is verified (if applicable).
7. Student record is created.
8. Enrollment workflow is initiated.

---

# Status Flow

Pending

↓

Viewed

↓

Accepted

OR

Declined

OR

Expired

↓

Enrollment Initiated

---

# Exceptions

- Offer Expired
- Offer Revoked
- Duplicate Acceptance
- Payment Failed
- Applicant Withdraws

---

# Notifications

- Offer Viewed
- Offer Accepted
- Offer Declined
- Enrollment Started

---

# Integrations

- Student Module
- Enrollment Module
- Finance Module
- Notification Service

---

# Result

Applicant becomes eligible for Enrollment and Student creation.