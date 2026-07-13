# Admission Offer Events

## Purpose

Defines domain events related to Admission Offers.

---

# Events

## AdmissionOfferGenerated

### Trigger

Admission Offer created.

### Consumers

- PDF Generator
- Audit Service

---

## AdmissionOfferPublished

### Trigger

Offer published.

### Consumers

- Applicant Portal
- Notification Service

---

## AdmissionOfferAccepted

### Trigger

Applicant accepts the Offer.

### Consumers

- Admission Acceptance Service
- Enrollment Service

---

## AdmissionOfferExpired

### Trigger

Offer expires.

### Consumers

- Notification Service

---

## AdmissionOfferRevoked

### Trigger

Offer revoked.

### Consumers

- Audit Service
- Notification Service