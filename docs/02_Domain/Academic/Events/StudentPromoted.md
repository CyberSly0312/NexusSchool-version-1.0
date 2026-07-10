# Academic Module
# Events
## StudentPromoted

Version : 1.0

---

# Objectif

StudentPromoted est déclenché lorsqu'un élève est officiellement promu vers une nouvelle classe, un nouveau niveau ou une nouvelle année académique.

Cet événement informe les autres modules que le parcours académique de l'élève a évolué.

---

# Déclencheur

L'événement est publié uniquement après :

- validation de la décision de promotion
- création de la nouvelle Enrollment
- clôture de l'ancienne Enrollment
- validation de la transaction

En cas de Rollback,

aucun événement ne doit être publié.

---

# Service émetteur

PromotionService

Méthode

promoteStudent()

---

# Payload

| Champ | Type | Description |
|--------|------|-------------|
| promotionId | UUID | Identifiant de la promotion |
| studentId | UUID | Élève concerné |
| previousEnrollmentId | UUID | Ancienne inscription |
| newEnrollmentId | UUID | Nouvelle inscription |
| previousAcademicYearId | UUID | Ancienne année |
| newAcademicYearId | UUID | Nouvelle année |
| previousClassroomId | UUID | Ancienne classe |
| newClassroomId | UUID | Nouvelle classe |
| promotionDecision | Enum | Décision |
| promotedBy | UUID | Utilisateur |
| promotedAt | Timestamp | Date de promotion |

---

# Décisions possibles

- Promoted
- Repeated
- Graduated
- Transferred

---

# Consommateurs possibles

Les modules suivants peuvent écouter cet événement :

- Gradebook
- Report Cards
- Attendance
- Timetable
- Finance
- Library
- Transport
- Hostel
- Parent Portal
- Notifications
- Audit

---

# Actions possibles

Gradebook

Préparer le nouveau carnet de notes.

---

Attendance

Créer le registre de présence.

---

Timetable

Associer le nouvel emploi du temps.

---

Finance

Créer les nouvelles obligations financières.

---

Library

Réinitialiser les emprunts annuels.

---

Notifications

Informer les parents et l'administration.

---

Audit

Historiser l'opération.

---

# Conditions de déclenchement

✓ Décision validée

✓ Nouvelle Enrollment créée

✓ Ancienne Enrollment clôturée

✓ Transaction validée

---

# Cas où l'événement ne doit pas être publié

- Promotion refusée

- Échec de création de la nouvelle Enrollment

- Rollback

- Exception

---

# Ordre d'exécution

Validation

↓

Décision

↓

Nouvelle Enrollment

↓

Clôture ancienne Enrollment

↓

Commit Transaction

↓

StudentPromoted

↓

Traitement des listeners

---

# Idempotence

Les consommateurs doivent ignorer les doublons.

Le traitement du même événement plusieurs fois ne doit produire aucun effet secondaire.

---

# Gestion des erreurs

Si un listener échoue :

- la promotion reste valide ;
- les autres listeners poursuivent leur traitement ;
- l'erreur est enregistrée ;
- un retry peut être exécuté.

---

# Sécurité

Le payload contient uniquement des identifiants métier.

Aucune donnée personnelle sensible ne doit être publiée.

---

# Audit

Les informations suivantes sont historisées :

- Event Name
- Promotion ID
- Student ID
- Previous Enrollment
- New Enrollment
- User ID
- Timestamp
- Tenant ID

---

# Tests

✓ Publication après Commit

✓ Aucun événement après Rollback

✓ Payload complet

✓ Payload valide

✓ Multi-tenant

✓ Retry des listeners

---

# Diagramme

```
PromotionService
        │
        ▼
Validation
        │
        ▼
Create New Enrollment
        │
        ▼
Close Previous Enrollment
        │
        ▼
Commit Transaction
        │
        ▼
StudentPromoted
        │
 ┌──────┼─────────────┬──────────────┐
 ▼      ▼             ▼              ▼
Gradebook Attendance Finance Notifications
```

---

# Résumé

StudentPromoted représente la transition officielle d'un élève vers une nouvelle étape de son parcours scolaire.

Il garantit :

- la synchronisation des modules académiques ;
- la création d'un nouvel historique annuel ;
- la cohérence entre les inscriptions successives ;
- une architecture événementielle fiable et extensible.