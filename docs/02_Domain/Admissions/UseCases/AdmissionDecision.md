# Admission Decision Use Cases

## Purpose

Defines all business processes related to Admission Decisions.

---

# Supported Use Cases

- Evaluate Application
- Approve Decision
- Reject Decision
- Waitlist Applicant
- Publish Decision
- Archive Decision

---

## Business Rules

- Evaluation must be completed before approval.
- Published Decisions cannot be modified.
- Approved Decisions automatically generate Admission Offers.

---

## Related Services

- AdmissionDecisionService

---

## Related Events

- AdmissionDecisionEvaluated
- AdmissionDecisionApproved
- AdmissionDecisionRejected
- AdmissionDecisionPublished

---

## Related Permissions

- admissions.decision.evaluate
- admissions.decision.approve
- admissions.decision.reject
- admissions.decision.publish