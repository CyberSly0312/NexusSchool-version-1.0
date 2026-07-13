# Admission Requirement Use Cases

## Purpose

Defines all Admission Requirement management operations.

---

# Supported Use Cases

- Create Requirement
- Update Requirement
- Activate Requirement
- Deactivate Requirement
- Assign Requirement to Program
- Archive Requirement

---

## Business Rules

- Requirement names are unique.
- Mandatory Requirements cannot be deleted.

---

## Related Services

- AdmissionRequirementService

---

## Related Events

- AdmissionRequirementCreated
- AdmissionRequirementActivated
- AdmissionRequirementArchived