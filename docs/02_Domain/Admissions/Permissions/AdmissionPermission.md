# Admission Permissions

## Purpose

Defines all permissions required to manage the Admissions domain.

---3

# Admission Applications

| Permission | Description |
|------------|-------------|
| admissions.application.view | View Admission Applications |
| admissions.application.create | Create Admission Applications |
| admissions.application.update | Edit Draft Applications |
| admissions.application.submit | Submit Applications |
| admissions.application.review | Review Applications |
| admissions.application.approve | Approve Applications |
| admissions.application.reject | Reject Applications |
| admissions.application.withdraw | Withdraw Applications |
| admissions.application.archive | Archive Applications |
| admissions.application.delete | Delete Applications |

---

# Admission Cycles

| Permission | Description |
|------------|-------------|
| admissions.cycle.view | View Admission Cycles |
| admissions.cycle.create | Create Admission Cycles |
| admissions.cycle.update | Update Admission Cycles |
| admissions.cycle.publish | Publish Admission Cycles |
| admissions.cycle.open | Open Admission Cycles |
| admissions.cycle.close | Close Admission Cycles |
| admissions.cycle.archive | Archive Admission Cycles |
| admissions.cycle.delete | Delete Admission Cycles |

---

# Admission Requirements

| Permission | Description |
|------------|-------------|
| admissions.requirement.view | View Requirements |
| admissions.requirement.create | Create Requirements |
| admissions.requirement.update | Update Requirements |
| admissions.requirement.activate | Activate Requirements |
| admissions.requirement.deactivate | Deactivate Requirements |
| admissions.requirement.archive | Archive Requirements |
| admissions.requirement.delete | Delete Requirements |

---

# Admission Documents

| Permission | Description |
|------------|-------------|
| admissions.document.view | View Documents |
| admissions.document.upload | Upload Documents |
| admissions.document.verify | Verify Documents |
| admissions.document.approve | Approve Documents |
| admissions.document.reject | Reject Documents |
| admissions.document.replace | Replace Documents |
| admissions.document.archive | Archive Documents |

---

# Admission Interviews

| Permission | Description |
|------------|-------------|
| admissions.interview.view | View Interviews |
| admissions.interview.schedule | Schedule Interviews |
| admissions.interview.update | Update Interviews |
| admissions.interview.start | Start Interviews |
| admissions.interview.evaluate | Evaluate Interviews |
| admissions.interview.complete | Complete Interviews |
| admissions.interview.cancel | Cancel Interviews |

---

# Admission Examinations

| Permission | Description |
|------------|-------------|
| admissions.exam.view | View Examinations |
| admissions.exam.schedule | Schedule Examinations |
| admissions.exam.register | Register Candidates |
| admissions.exam.grade | Grade Examinations |
| admissions.exam.publish | Publish Results |
| admissions.exam.cancel | Cancel Examinations |

---

# Admission Decisions

| Permission | Description |
|------------|-------------|
| admissions.decision.view | View Decisions |
| admissions.decision.evaluate | Evaluate Applications |
| admissions.decision.approve | Approve Decisions |
| admissions.decision.reject | Reject Decisions |
| admissions.decision.waitlist | Manage Waiting List |
| admissions.decision.publish | Publish Decisions |

---

# Admission Offers

| Permission | Description |
|------------|-------------|
| admissions.offer.view | View Offers |
| admissions.offer.generate | Generate Offers |
| admissions.offer.publish | Publish Offers |
| admissions.offer.revoke | Revoke Offers |
| admissions.offer.extend | Extend Offer Validity |

---

# Admission Acceptances

| Permission | Description |
|------------|-------------|
| admissions.acceptance.view | View Acceptances |
| admissions.acceptance.accept | Accept Offers |
| admissions.acceptance.decline | Decline Offers |
| admissions.acceptance.validate | Validate Acceptances |

---

# Admission Checklists

| Permission | Description |
|------------|-------------|
| admissions.checklist.view | View Checklists |
| admissions.checklist.generate | Generate Checklists |
| admissions.checklist.update | Update Checklists |
| admissions.checklist.approve | Approve Checklists |
| admissions.checklist.authorize | Authorize Enrollment |
| admissions.checklist.archive | Archive Checklists |

---

# Recommended Default Role Mapping

| Role | Permissions |
|------|-------------|
| Super Administrator | All |
| School Administrator | All Admissions Permissions |
| Admission Manager | Full Admissions Management |
| Admission Officer | Operational Permissions |
| Interview Panel | Interview Permissions |
| Examiner | Examination Permissions |
| Registrar | Acceptance & Enrollment Permissions |
| Applicant | Submit Application, Upload Documents, Accept/Decline Offer |

---

# Security Notes

- Permissions are tenant-scoped.
- All sensitive operations must be audited.
- Permission checks must be enforced at Policy and Service levels.
- High-risk actions should support optional approval workflows.