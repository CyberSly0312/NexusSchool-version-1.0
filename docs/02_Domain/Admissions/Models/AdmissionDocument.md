# Admission Document Model

## Purpose

The Admission Document represents a document submitted by an Applicant as part of the admission process.

Admission Documents are used to verify the Applicant's identity, academic qualifications, legal status, medical compliance, and other institutional requirements before an admission decision is made.

Each document is linked to an Admission Application and may undergo one or more verification processes.

---

# Business Objectives

The Admission Document enables the institution to:

- Collect applicant documents.
- Verify document authenticity.
- Track document submission.
- Support digital admissions.
- Reduce paper-based administration.
- Maintain secure document storage.
- Improve regulatory compliance.

---

# Responsibilities

The Admission Document is responsible for:

- Storing applicant documents.
- Recording document metadata.
- Managing document verification.
- Supporting document approval.
- Supporting document rejection.
- Maintaining document history.

---

# Attributes

## Identification

- ID
- UUID
- Document Number

---

## Document Information

- Document Type
- Document Name
- File Name
- File Extension
- File Size
- MIME Type

---

## Ownership

- Admission Application
- Applicant

---

## Verification

- Verification Status
- Verified By
- Verification Date
- Verification Notes

---

## Validity

- Issue Date
- Expiration Date
- Is Expired
- Is Mandatory

---

## Storage

- Storage Provider
- Storage Path
- File URL
- Checksum

---

## Status

- Draft
- Uploaded
- Under Verification
- Approved
- Rejected
- Archived

---

## Audit Information

- Uploaded At
- Updated At
- Approved At
- Archived At

---

# Relationships

The Admission Document belongs to:

- Admission Application
- Applicant
- Admission Requirement

The Admission Document may have:

- Verification Records
- Audit Logs
- Comments

---

# Lifecycle

Draft

↓

Uploaded

↓

Under Verification

↓

Approved

or

↓

Rejected

↓

Archived

---

# Business Rules

- Every mandatory document must be uploaded before admission approval.
- Documents may be rejected if unreadable or invalid.
- Expired documents require replacement.
- Only approved documents satisfy admission requirements.
- Archived documents cannot be modified.

---

# Constraints

- Every document must belong to one Admission Application.
- File size must respect system limits.
- Unsupported file types are rejected.
- Duplicate mandatory documents are prohibited unless versioned.

---

# Security

Applicants may:

- Upload documents.
- Replace rejected documents.
- View their own documents.

Admission Officers may:

- Review documents.
- Approve documents.
- Reject documents.
- Add verification notes.

Administrators may:

- Archive documents.
- Configure document types.

---

# Audit Requirements

The system shall record:

- Upload history.
- Download history.
- Verification history.
- Approval history.
- Rejection history.
- Replacement history.

---

# Notifications

Applicants should be notified when:

- Document uploaded successfully.
- Document approved.
- Document rejected.
- Document expires.
- Additional documents requested.

Admission Officers should receive notifications when:

- New document uploaded.
- Document awaiting verification.
- Verification overdue.

---

# Reporting

Reports include:

- Documents by Type.
- Verification Status.
- Missing Documents.
- Expired Documents.
- Approval Rate.
- Rejection Rate.

---

# Integration

The Admission Document integrates with:

- Admissions
- Documents
- Identity Verification
- Notification System
- Reporting
- Student Management

---

# Future Improvements

- OCR Extraction.
- AI Fraud Detection.
- QR Code Verification.
- Government Registry Validation.
- Digital Signature Verification.
- Blockchain Document Verification.
- Multi-cloud Storage Support.