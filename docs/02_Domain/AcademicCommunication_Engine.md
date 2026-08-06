# Academic Communication Engine

> SPR-012 — the single official document & publication layer of NexusSchool.
> See the [SPR-012 report](../00_Project_Management/SPR-012_Academic_Communication_Engine_Report.md)
> and [ADR-012](../ADR/ADR-012.md).

## Purpose

Generates, versions, approves, publishes and archives every official academic
document (report cards, council minutes, summaries, transcripts, certificates,
official letters, …). No business engine generates documents itself: they
dispatch through the engine's contracts and generators.

---

## Responsibilities

- Permanent document identity & numbering
- Forward-only document lifecycle (draft → generated → reviewed → approved →
  published → archived)
- Immutable versioning with `is_current` official file rotation
- Approval workflow (configurable per publication policy)
- Policy-driven visibility redaction per audience scope
- Pluggable publication channels (portals, PDF export; email/SMS/push later)
- Per-recipient delivery traceability (`PublicationRecord` +
  `PublicationDeliveryAttempt`)
- Append-only audit journal
- School (tenant) isolation on every query
- Capability-based authorization (`document.*`)

---

## Inputs

- Business-engine data via **Domain Contracts** (Assessment, Gradebook,
  Attendance, Class Council, Guardian, Staff, Academic Framework, Academic
  Policies, Student Identity, School) — implemented by Infrastructure adapters
- School-scoped `AcademicDocumentTemplate`, `PublicationPolicy`,
  `PublicationChannel` configuration
- Actor actions: generate, review, approve, publish, archive, regenerate

---

## Outputs

- `AcademicDocument` aggregate with permanent `document_number`
- Immutable `AcademicDocumentVersion` rows + official `GeneratedFile`
- `PublicationRecord` / `PublicationDeliveryAttempt` delivery trail
- `DocumentRecipient`, `DocumentSignature`, `DocumentApproval`, `DocumentAudit`
- Rendered HTML (portals) and PDF export (official file)

---

## Business Rules

- **Forward-only lifecycle.** `DocumentStatus::allowsTransitionFrom()` permits
  exactly the documented edges; nothing moves backwards, `draft` is never
  reachable again. Documents whose policy requires no approval go straight
  `Generated → Published`.
- **Immutability.** `isImmutable()` is true for `published` and `archived`.
  A correction goes through `RegenerateDocumentAction`: new immutable version,
  re-rendered official file, `GeneratedFile.is_current` flipped on **all** of
  the document's files. The permanent `document_number` is kept.
- **Numbering.** `{platform}-{year}-{school}-{type}-{9-digit seq}`; sequence is
  count-based **per school and per document type**; regeneration never changes
  the identity.
- **Approval.** A policy may require approval (one of the configured approval
  roles) or not. `ApproveDocumentAction` validates the policy's approval roles
  and the document's school; rejection flags the document as rejected.
- **Visibility.** A `VisibilityPolicy` maps every `VisibilityScope`
  (director / secretary / teacher / parent / student / inspector) to the
  document sections it may see. Parents and students are always self-scoped.
- **Publication.** One `PublicationRecord` per (document, channel, recipient).
  At least one delivery must complete, otherwise
  `DocumentPublicationFailedException::noDelivery()` is thrown. Completed
  records are never mutated — a re-delivery creates a new record.
- **Channels.** Implement `PublicationChannelContract`, declare the driver in
  `config/nexus-academic-communication.php`, enable it per school. Un-delivered
  channels (`email`, `sms`, `push_notification`) are refused by the dispatcher
  via `DocumentChannel::isImplemented()`.
- **Authorization.** `document.view / generate / review / approve / publish /
  archive / template.manage` — capability-based, never role-based, with the
  `super_admin` bypass and school scoping on every check. Deletion is disabled.
- **Tenant isolation.** Templates, policies, channels, documents, versions and
  files are scoped per school; cross-school templates / policies / approvals
  are refused.

---

## Produced Events

- `DocumentGenerated`
- `DocumentVersionCreated`
- `DocumentApproved`
- `DocumentPublished`
- `DocumentArchived`
- `PublicationCompleted`
- `PublicationCancelled`

Consumed by `AcademicCommunicationAuditLogger` → `document_audits`.

---

## Future Improvements

- Real delivery drivers: `email`, `sms`, `push_notification`
- Full PDF rendering fidelity
- Digital signature verification
- Public verification portal (verify a document number)
