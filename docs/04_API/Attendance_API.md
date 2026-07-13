# Attendance API

## Purpose

Manage attendance records for students, teachers, and staff.

---

# Base Endpoint

/api/v1/attendance

---

# Endpoints

GET /attendance

List attendance records.

GET /attendance/{id}

Retrieve attendance details.

POST /attendance

Record attendance.

PATCH /attendance/{id}

Update attendance.

DELETE /attendance/{id}

Remove attendance record.

---

# Additional Endpoints

POST /attendance/bulk

Bulk attendance registration.

GET /attendance/statistics

Attendance analytics.

GET /attendance/report

Attendance reports.

GET /attendance/export

Export attendance.

---

# Filters

student

teacher

class

date

status

academic_year

---

# Search

?q=

---

# Permissions

attendance.view

attendance.create

attendance.update

attendance.delete

attendance.export

---

# Business Rules

Only one attendance record per person per session.

Attendance cannot be modified after approval without authorization.

---

# Audit

Every attendance modification is fully logged.