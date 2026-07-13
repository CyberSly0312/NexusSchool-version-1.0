# Webhook API

## Purpose

Allow Nexus School to communicate with external systems using secure event-driven webhooks.

---

## Supported Integrations

- Mobile Money
- Payment Gateways
- SMS Providers
- Email Providers
- Learning Platforms
- Government Systems
- ERP Integrations

---

## Trigger Events

Subscription Activated

Subscription Renewed

Payment Completed

Invoice Paid

Student Registered

Student Graduated

Teacher Created

Attendance Recorded

Result Published

---

## Request Method

POST

---

## Payload Format

application/json

---

## Required Headers

Content-Type

application/json

X-Signature

Webhook signature

X-Tenant

Tenant Identifier

---

## Security

- HTTPS required
- Signature verification
- Replay attack prevention
- Timestamp validation
- IP whitelisting (optional)

---

## Retry Policy

Failed requests are retried automatically.

Retry intervals

- 1 minute
- 5 minutes
- 15 minutes
- 1 hour
- 24 hours

---

## Failure Handling

After maximum retries:

- Event marked failed
- Administrator notified
- Retry available manually

---

## Audit

Every webhook request and response is logged.