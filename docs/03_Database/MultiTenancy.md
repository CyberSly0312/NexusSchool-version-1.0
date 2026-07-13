# Multi-Tenancy Strategy

## Purpose

Provide complete data isolation between schools while allowing centralized platform management.

---

## Architecture

- One Landlord Database
- One Database per Tenant

---

## Landlord Stores

- Tenants
- Subscriptions
- Billing
- Domains
- Plans
- Licenses
- Global Settings

---

## Tenant Stores

- Students
- Teachers
- Finance
- Academic
- Library
- Attendance
- HR

---

## Tenant Isolation

No tenant can access another tenant's database.

---

## Provisioning

New tenant creation is fully automated after successful subscription payment.

---

## Benefits

- Better security
- Easier backups
- Independent scaling
- Easier maintenance