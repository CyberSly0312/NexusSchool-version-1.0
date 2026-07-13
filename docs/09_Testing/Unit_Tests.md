# Unit Testing Strategy

## Purpose

Ensure individual classes, services, and business rules behave correctly in isolation.

---

# Objectives

- Verify business logic
- Detect regressions early
- Enable safe refactoring
- Improve code quality

---

# Scope

Unit tests cover:

- Services
- Enums
- DTOs
- Validation Rules
- Helper Classes
- Utility Functions

---

# Exclusions

Unit tests should not access:

- Database
- File System
- External APIs
- Network Resources

These belong to integration or feature tests.

---

# Framework

Preferred:

- PHPUnit
- Pest (optional)

---

# Coverage Goals

Target:

Minimum 80%

Critical business services:

90%+

---

# Naming Convention

Examples

StudentServiceTest

InvoiceCalculatorTest

AttendanceValidatorTest

---

# Best Practices

- One assertion per behavior
- Independent tests
- Fast execution
- Deterministic results

---

# Continuous Integration

Unit tests execute on every pull request.