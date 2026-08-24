GOVERNANCE RECONCILIATION — SPR-014
Status
SPR-014 = DEFERRED — pending proven architectural need.
SPR-015 = NEXT ACTIVE SPRINT (Discovery only).
Files modified (exact list)
File	Changes
docs/docs/00_Project_Management/SPR-014_Records_Lifecycle_Engine_Report.md	Verdict → NEEDS REWORK → DEFERRED ; section Architectural Decision Brief — 2026-08-24 ajoutée (intent, existing coverage, real gap, non-sponsorship reasons, SPR-015 independence, Phase 9 Cloud Archive, reactivation conditions, decision record)
docs/docs/00_Project_Management/CURRENT_POSITION.md	Current Sprint → SPR-015 (Discovery) ; Sprint Status + ⏸️ deferred note ; §5 Planned Sprints annoté ; §9 Priorities réordonnées (Priority 1 = SPR-015, Records Lifecycle en "Deferred")
docs/docs/00_Project_Management/PROJECT_STATE.md	§5 Domain Status : Records Lifecycle ⏳ Deferred — gap covered by existing domains; revisit with ADR ; §8 priorités réordonnées identiques
docs/docs/SESSION_HANDOFF.md	§2 Current Sprint → SPR-015 + SPR-014 Status deferred ; §3 objectif réécrit avec raison d'audit conservée ; §5 WIP → SPR-015 discovery ; §6 Immediate Next Sprint → SPR-015 objectives + bloc Deferred ; §11 fichiers de la réconciliation listés
docs/docs/00_Project_Management/CHANGELOG.md	Entrée datée 2026-08-24 conforme aux §9 Change Management Rules (Date/Version/Sprint/Summary/Impact/Documentation) ; Sprint History : SPR-014 ⏸️ Deferred, SPR-015 🟡 Discovery ; timeline v0.4.0 annotée
docs/docs/00_Project_Management/MASTER_ROADMAP.md	Phase 3 : ligne SPR-014 annotée "Deferred — absorbed into SPR-015 prerequisites & Phase 9 Enterprise Services (Cloud Archive)" ; §5 Current Position → SPR-015
Historique préservé partout — seules les sections d'état courant ont été modifiées.
Files intentionally untouched
- app/ — 0 modification
- database/ — 0 modification
- tests/ — 0 création/modification
- config/, routes/ — 0 modification
- docs/ADR/ — aucun ADR créé, modifié ou rempli (20 fichiers, tailles inchangées)
Validation
- git diff --check : ✅ propre (aucune erreur whitespace)
- git status --porcelain : uniquement les 6 fichiers gouvernance ci-dessus (+ report untracked)
- Aucun fichier *RecordLifecycle* dans le repository : 0
- app/Domains/RecordsLifecycle : n'existe pas
- Cohérence croisée vérifiée par grep : les 6 documents racontent la même histoire — SPR-014 = DEFERRED · SPR-015 = NEXT ACTIVE SPRINT · Cloud Archive = FUTURE / PHASE 9 · aucun RecordsLifecycle BC implémenté
- Aucun commit effectué
NEXT IMPLEMENTATION POSITION
NEXT SPRINT: SPR-015 — Certificate & Transcript Engine
NEXT TASK: SPR-015 DISCOVERY ONLY
1. Lire les contrats existants : ReportCard (ReportCardContentComposer, snapshots), ClassCouncil (ClassCouncilContract::decisionsForStudent), Gradebook (GradebookContract::studentStanding), AcademicPolicies (AcademicPoliciesContract), Enrollment (EnrollmentGraduation)
2. Définir les business rules de Transcript et Certificate
3. Identifier exactement quelles données publiées sont nécessaires
4. Vérifier les générateurs existants dans AcademicCommunication (DocumentType::Transcript TR / DocumentType::Certificate CE déjà déclarés, pattern des 6 générateurs)
5. Produire un SPR-015 discovery report AVANT toute modification de code
STOP respecté — aucune implémentation SPR-015 ni SPR-014 dans cette session.
