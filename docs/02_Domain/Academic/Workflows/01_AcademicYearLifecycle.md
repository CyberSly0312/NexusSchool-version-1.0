# Academic Year Lifecycle Workflow

## Purpose

This workflow describes the complete lifecycle of an Academic Year, from creation to archival.

It ensures that only one Academic Year is active at any given time and that academic operations follow a controlled process.

---

# Actors

- Super Admin
- School Administrator
- System

---

# Preconditions

- User is authenticated.
- User has permission to manage Academic Years.
- School exists.

---

# Trigger

User selects:

Academic Management

↓

Academic Years

↓

Create Academic Year

---

# Main Flow

## Step 1

Create a Draft Academic Year.

Status

Draft

---

## Step 2

Configure:

- Name
- Start Date
- End Date

---

## Step 3

Create Academic Terms.

Example

First Term

Second Term

Third Term

---

## Step 4

Create Classrooms.

---

## Step 5

Assign Subjects.

---

## Step 6

Verify Academic Configuration.

System checks:

- Terms exist
- Classrooms exist
- Subjects configured

---

## Step 7

Activate Academic Year.

Status

Active

---

## Step 8

Open Enrollment Period.

Students may now be enrolled.

---

## Step 9

Begin Academic Activities.

Examples

- Attendance
- Timetable
- Grades
- Assessments

---

## Step 10

Close Academic Year.

Requirements

- All Terms Closed
- Grades Finalized
- Report Cards Generated

Status

Closed

---

## Step 11

Archive Academic Year.

Status

Archived

Historical data remains available.

---

# Alternative Flows

## A1

Academic Year saved as Draft for later completion.

---

## A2

Activation postponed until configuration is complete.

---

# Exception Flows

## E1

Configuration incomplete.

Result

Activation rejected.

---

## E2

Another Academic Year is already Current.

Result

Activation rejected.

---

## E3

Academic Year dates overlap.

Result

Creation rejected.

---

# Postconditions

- Academic Year archived.
- Historical records preserved.
- Reports remain accessible.

---

# Related Policies

ACA-001

ACA-002

ACA-005

ACA-009

ACA-013

---

# Future Improvements

- Automatic Academic Year creation
- Ministry calendar synchronization
- AI validation of academic configuration
- Automatic promotion workflow initiation