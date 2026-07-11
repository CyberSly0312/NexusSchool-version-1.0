# Admission Cycle Service

## Purpose

The Admission Cycle Service manages Admission Cycles throughout their lifecycle.

It ensures that institutional admission periods are correctly configured and controlled.

---

# Responsibilities

- Create Admission Cycles.
- Update Admission Cycles.
- Publish Admission Cycles.
- Open Admission Cycles.
- Close Admission Cycles.
- Archive Admission Cycles.
- Validate date ranges.
- Monitor active Admission Cycles.

---

# Dependencies

- AcademicYearService
- NotificationService
- AuditService
- EventDispatcher

---

# Public Methods

## createCycle()

Creates a new Admission Cycle.

---

## updateCycle()

Updates an existing Cycle.

---

## publishCycle()

Publishes a Draft Cycle.

---

## openCycle()

Opens the Admission Cycle.

---

## closeCycle()

Closes the Admission Cycle.

---

## archiveCycle()

Archives the Admission Cycle.

---

## validateSchedule()

Validates the configured dates.

---

## getActiveCycle()

Returns the active Admission Cycle.

---

# Business Rules

- Only one active Admission Cycle per intake.
- Closed Cycles reject new Applications.
- Archived Cycles are immutable.
- Date ranges cannot overlap for the same intake.

---

# Database Transactions

- Create Cycle
- Publish Cycle
- Open Cycle
- Close Cycle
- Archive Cycle

---

# Events Dispatched

- AdmissionCycleCreated
- AdmissionCyclePublished
- AdmissionCycleOpened
- AdmissionCycleClosed
- AdmissionCycleArchived

---

# Exceptions

- DuplicateAdmissionCycleException
- InvalidDateRangeException
- ActiveCycleAlreadyExistsException
- InvalidCycleStateException

---

# Security

- Tenant isolation enforced.
- Administrative authorization required.
- Complete audit logging.

---

# Future Improvements

- Automatic Cycle Scheduling.
- Capacity Forecasting.
- Multi-campus Admission Cycles.
- Calendar Synchronization.