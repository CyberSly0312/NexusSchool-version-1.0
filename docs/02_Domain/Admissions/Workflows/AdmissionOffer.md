# Admission Offer Workflow

## Purpose

This workflow manages the issuance of Admission Offers after successful Admission Decisions.

---

# Trigger

Admission Decision is Approved.

---

# Preconditions

- Approved Admission Decision.
- Admission Cycle active.
- Program Capacity available.

---

# Workflow Steps

1. Generate Admission Offer.
2. Assign Offer Number.
3. Generate Official Letter.
4. Publish Offer.
5. Notify Applicant.
6. Wait for Applicant Response.
7. Accept, Decline or Expire Offer.

---

# Status Flow

Draft

↓

Generated

↓

Published

↓

Accepted

OR

Declined

OR

Expired

---

# Exceptions

- Offer Revoked
- Capacity Full
- Expiration Reached
- Applicant Withdraws

---

# Notifications

- Offer Published
- Offer Accepted
- Offer Declined
- Offer Expired

---

# Result

Offer proceeds to Admission Acceptance or closes the admission process.