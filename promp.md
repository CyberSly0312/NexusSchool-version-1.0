CONTINUE SPR-014 FROM THE CURRENT AUDIT STATE.

IMPORTANT:
Do NOT implement any RecordsLifecycle engine.
Do NOT create RecordLifecycle models, state machines, generic lifecycle abstractions, contracts, adapters, migrations, factories, or services yet.

The repository audit has established that the previously claimed SPR-014 implementation does NOT exist in this repository. The current repository is the source of truth.

CURRENT CONFIRMED STATE:

- HEAD: ac8acc2 on main
- Working tree was clean before the audit
- No RecordLifecycle / RecordsLifecycle implementation exists
- No RecordsLifecycle tests exist
- No SPR-014 implementation artifacts exist
- SPR-014 report has now been created:
  docs/docs/00_Project_Management/SPR-014_Records_Lifecycle_Engine_Report.md
- Report status:
  Audit / Phase 0-1 — NO IMPLEMENTATION
- Report verdict:
  NEEDS REWORK

The audit established:

1. AcademicCommunication already owns:
   - permanent AcademicDocument identity
   - immutable document versions
   - publication
   - archival
   - publication records
   - audit trail
   - tenant isolation
   - contract-based cross-domain consumption

2. ClassCouncil already owns:
   - CouncilSession lifecycle
   - published CouncilDecision as the official academic outcome
   - publication
   - archival
   - audit
   - tenant isolation
   - contract boundaries

3. ReportCard already owns:
   - ReportCard lifecycle
   - generation
   - publication
   - archival
   - regeneration
   - delegation of official academic documents to AcademicCommunication

4. Shared Kernel contains no suitable lifecycle abstraction.

5. No explicit retention mechanism exists.

6. No explicit long-term preservation/archive mechanism exists.

7. No consolidated permanent academic record per learner currently exists.

8. Storage-provider decisions are still pending.

9. ADR-012 explicitly establishes AcademicCommunication as the publication/document layer and already owns identity, versioning, publication and archival.

Therefore:
A generic cross-domain "Records Lifecycle Engine" would duplicate existing domain responsibilities and violate established architecture.

CURRENT SPR-014 REPORT:
Read the entire file:
docs/docs/00_Project_Management/SPR-014_Records_Lifecycle_Engine_Report.md

Do NOT contradict its findings unless you can prove a contradiction directly from repository code.

==================================================
PHASE 1 — ROADMAP / GOVERNANCE RECONCILIATION
==================================================

Before changing anything, inspect:

- docs/docs/00_Project_Management/MASTER_ROADMAP.md
- docs/docs/00_Project_Management/CURRENT_POSITION.md
- docs/docs/00_Project_Management/PROJECT_STATE.md
- docs/docs/SESSION_HANDOFF.md
- all relevant SPR-015 documentation
- all relevant project journal entries concerning:
  - SPR-014
  - permanent academic records
  - retention
  - archival
  - storage
  - transcripts
  - certificates

Determine:

A. What was the ORIGINAL business objective of SPR-014?
B. Which parts of that objective are already solved by existing bounded contexts?
C. Which parts are genuinely unsolved?
D. Which unsolved parts are actually prerequisites for SPR-015?
E. Which decisions are still pending?
F. Whether SPR-014 should remain a bounded context at all.

Do not assume the roadmap wording is architecturally correct.
The implementation and accepted ADRs are the source of truth.

==================================================
PHASE 2 — DEFINE THE REAL GAP
==================================================

Produce a concise architectural gap analysis.

Explicitly classify each candidate responsibility:

- Document identity
- Document versioning
- Publication
- Archival
- Audit
- Tenant isolation
- Retention policy
- Legal retention / purge
- Long-term preservation
- Archive export/package
- Object storage
- Learner academic history
- Transcript projection
- Certificate projection
- Cross-year academic record
- Record discovery/search

For every item classify it as:

OWNED BY EXISTING DOMAIN
UNSOLVED GAP
SPR-015 RESPONSIBILITY
FUTURE / DEFERRED
NOT JUSTIFIED

Do not invent responsibilities.

==================================================
PHASE 3 — INVESTIGATE SPR-015
==================================================

This is critical.

Inspect all SPR-015 documentation and existing code related to:

- Transcript
- Certificate
- Student academic history
- Academic records
- Graduation
- Completion
- Learner permanent record

Determine whether SPR-015 actually requires:

- a persistent academic record projection,
- a retention service,
- archival storage,
- historical record aggregation,
- or merely contracts from existing engines.

Do not design the solution yet.

Produce a short dependency statement:

"SPR-015 requires X from SPR-014 because Y."

If SPR-015 does not actually require SPR-014, say so explicitly.

==================================================
PHASE 4 — STORAGE / PRESERVATION DECISION AUDIT
==================================================

Inspect:

- SESSION_HANDOFF
- architecture docs
- environment/config files
- existing filesystem storage abstraction
- Laravel filesystem configuration
- any S3/object storage references
- archive/export code
- existing document storage mechanisms

Determine what storage capabilities already exist.

Do NOT choose an external provider unless the repository already establishes one.

If provider selection is genuinely pending, document it as a decision dependency.

==================================================
PHASE 5 — ARCHITECTURAL OPTIONS
==================================================

Based only on the repository findings, propose at most THREE options:

OPTION A:
Re-specify SPR-014 as a Retention & Preservation bounded context.

OPTION B:
Do not create a new bounded context.
Implement retention/preservation capabilities inside the existing relevant infrastructure/domain boundaries.

OPTION C:
Defer SPR-014 and make SPR-015 consume existing AcademicCommunication/ClassCouncil/ReportCard contracts directly.

For each option evaluate:

- DDD fit
- ADR compatibility
- duplication risk
- coupling
- implementation complexity
- usefulness to SPR-015
- storage dependency
- future extensibility

Then recommend ONE option.

==================================================
PHASE 6 — ADR REQUIREMENT
==================================================

Do NOT create the ADR immediately.

First determine:

- whether an existing ADR can cover the decision;
- whether a new ADR is genuinely required;
- what exact architectural decision the ADR would record.

If a new ADR is required, prepare a DRAFT only.

Do not overwrite existing ADRs.
Do not fill unrelated empty ADR files.
Do not invent an ADR number.

The draft should contain:

- Context
- Problem
- Existing architecture
- Decision
- Explicit non-goals
- Boundaries
- Consequences
- Dependencies
- Relationship to ADR-012 and ADR-013
- Relationship to SPR-015

==================================================
PHASE 7 — GOVERNANCE UPDATE PLAN
==================================================

Do not modify CURRENT_POSITION, PROJECT_STATE, CHANGELOG, or SESSION_HANDOFF yet.

First provide the exact proposed changes for:

- SPR-014 report
- CURRENT_POSITION
- PROJECT_STATE
- SESSION_HANDOFF
- CHANGELOG
- possible ADR

Show:

FILE
SECTION
CHANGE
REASON

==================================================
STOP CONDITION
==================================================

STOP BEFORE WRITING ANY APPLICATION CODE.

At the end, provide:

SPR-014 ARCHITECTURAL DECISION BRIEF

1. Original intent
2. What already exists
3. Real architectural gap
4. SPR-015 dependency
5. Storage dependency
6. Options
7. Recommended option
8. Whether a new ADR is required
9. Proposed new SPR-014 scope
10. Explicit non-goals
11. Files that would need documentation updates
12. Exact next implementation step, but DO NOT IMPLEMENT IT

CRITICAL RULES:

- Repository is the source of truth.
- Existing accepted ADRs outrank roadmap assumptions.
- Do not recreate missing historical work as if it existed.
- Do not implement a generic lifecycle abstraction.
- Do not modify application code.
- Do not modify existing ADRs.
- Do not create an ADR until the architectural decision is demonstrated to be necessary.
- Do not create migrations.
- Do not create models.
- Do not create tests.
- Do not create factories.
- Do not create contracts/adapters.
- Do not commit anything.

This phase is ARCHITECTURE ONLY.
