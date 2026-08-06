# NexusSchool Enterprise Platform

# Documentation Index

**Version:** 1.0.0

**Status:** Active

**Owner:** NexusSchool Architecture Team

**Last Updated:** 05 August 2026

---

# Welcome

Welcome to the official documentation of the NexusSchool Enterprise Platform.

This documentation is organized to support long-term development, architecture governance, business implementation, operational management and future platform evolution.

Every contributor should begin here before navigating to specific documentation.

---

# Documentation Structure

```text
docs/
│
├── README.md
│
├── 00_Project_Management/
│
├── 01_Architecture/
│
├── 02_Domain/
│
├── 03_Database/
│
├── 04_API/
│
├── 05_Testing/
│
├── 06_ADR/
│
├── 07_Security/
│
├── 08_Deployment/
│
├── 09_User_Guides/
│
├── 10_Blueprints/
│
├── 99_Project_Journal/
│
└── SESSION_HANDOFF.md
```

---

# Start Here

For a first-time contributor, read the documents in the following order.

1. MASTER_ROADMAP.md
2. VISION_2030.md
3. ARCHITECTURE_DECISIONS.md
4. DEPENDENCY_MAP.md
5. CURRENT_POSITION.md
6. PROJECT_STATE.md
7. SESSION_HANDOFF.md

This sequence provides enough context to understand the platform before writing any code.

---

# Project Management

Location

```text
docs/00_Project_Management/
```

Contains

* Master Roadmap
* Architecture Decisions
* Dependency Map
* Vision 2030
* Current Position
* Project State
* Risk Register
* Quality Dashboard
* Technical Debt Register
* Changelog

---

# Architecture

Location

```text
docs/01_Architecture/
```

Contains

* Platform Architecture
* Panel Architecture
* Workflow Documentation
* Event Flow
* Integration Guides
* Architectural Diagrams

---

# Business Domains

Location

```text
docs/02_Domain/
```

Contains

* Domain READMEs
* Business Rules
* Aggregates
* Value Objects
* Domain Contracts
* Extension Guides

---

# Database

Location

```text
docs/03_Database/
```

Contains

* ER Diagrams
* Database Standards
* Schema Documentation
* Migration Strategy

---

# APIs

Location

```text
docs/04_API/
```

Contains

* Internal APIs
* Public APIs
* Webhooks
* Integration Documentation

---

# Testing

Location

```text
docs/05_Testing/
```

Contains

* Testing Strategy
* Pest Guidelines
* Contract Testing
* Architecture Testing
* Performance Testing

---

# Architecture Decision Records

Location

```text
docs/06_ADR/
```

Contains

All approved Architecture Decision Records.

Every architectural change requiring governance shall be documented here.

---

# Security

Location

```text
docs/07_Security/
```

Contains

* Security Standards
* Tenant Isolation
* Authorization
* Encryption
* Audit Strategy

---

# Deployment

Location

```text
docs/08_Deployment/
```

Contains

* Production Deployment
* Infrastructure
* CI/CD
* Monitoring
* Disaster Recovery

---

# User Guides

Location

```text
docs/09_User_Guides/
```

Contains

Administrator documentation.

Teacher documentation.

Student documentation.

Parent documentation.

Secretary documentation.

Accountant documentation.

---

# Blueprints

Location

```text
docs/10_Blueprints/
```

Contains

Complete functional specifications for every Business Engine.

Blueprints are the authoritative reference before implementation.

---

# Project Journal

Location

```text
docs/99_Project_Journal/
```

Contains

Daily engineering journals.

Development history.

Engineering notes.

Session summaries.

---

# Operational Continuity

Location

```text
docs/SESSION_HANDOFF.md
```

Purpose

Maintain continuity between development sessions.

This document shall be updated at the end of every working session.

---

# Documentation Rules

Every new document shall

* have a clear purpose;
* follow the established documentation standards;
* include version information;
* identify its owner;
* be maintained throughout the project lifecycle.

Duplicate documentation should be avoided.

Whenever possible, update an existing document instead of creating a new one.

---

# Final Statement

Documentation is considered part of the software.

Every architectural decision, business rule and implementation shall remain traceable through this documentation structure.

The documentation shall evolve together with the platform and remain the single source of truth for the NexusSchool project.
