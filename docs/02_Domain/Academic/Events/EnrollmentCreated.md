# Academic Module
# Events
## EnrollmentCreated

Version : 1.0

---

# Objectif

EnrollmentCreated est déclenché lorsqu'une inscription académique est créée avec succès.

Une inscription représente le dossier académique annuel d'un élève.

Cet événement informe les autres modules qu'un nouvel élève est désormais actif pour une année scolaire.

---

# Déclencheur

L'événement est publié uniquement après :

- validation complète des données
- création de l'Enrollment
- validation de la transaction

Si une erreur survient avant le Commit,

aucun événement ne doit être publié.

---

# Service émetteur

EnrollmentService

Méthode

create()

---

# Payload

| Champ | Type | Description |
|--------|------|-------------|
| enrollmentId | UUID | Identifiant de l'inscription |
| studentId | UUID | Élève concerné |
| schoolId | UUID | École |
| academicYearId | UUID | Année académique |
| classroomId | UUID | Classe |
| sectionId | UUID / Null | Section |
| status | Enum | Statut |
| enrollmentDate | Date | Date d'inscription |
| createdBy | UUID | Utilisateur |
| createdAt | Timestamp | Date de création |

---

# Consommateurs possibles

Les modules suivants peuvent écouter cet événement :

- Attendance
- Timetable
- Gradebook
- Finance
- Library
- Transport
- Hostel
- Medical
- Parent Portal
- Notifications
- Audit

---

# Actions possibles

Attendance

Créer le registre de présence.

---

Timetable

Associer l'emploi du temps.

---

Finance

Créer le compte de facturation.

---

Library

Créer le compte bibliothèque.

---

Transport

Préparer l'affectation au transport scolaire.

---

Parent Portal

Autoriser les parents à suivre cette inscription.

---

Notifications

Informer les responsables administratifs.

---

Audit

Journaliser l'opération.

---

# Conditions de déclenchement

✓ Enrollment valide

✓ Classe active

✓ Année académique active

✓ Capacité disponible

✓ Transaction validée

---

# Cas où l'événement ne doit pas être publié

- Validation échouée

- Classe complète

- Année académique fermée

- Rollback

- Exception

---

# Ordre d'exécution

Validation

↓

Création Enrollment

↓

Commit Transaction

↓

EnrollmentCreated

↓

Traitement des listeners

---

# Idempotence

Les listeners doivent supporter la réception multiple du même événement.

Aucun doublon ne doit être créé.

---

# Gestion des erreurs

En cas d'échec d'un listener :

- l'inscription reste valide ;
- les autres listeners poursuivent leur exécution ;
- l'erreur est enregistrée ;
- un mécanisme de retry peut être appliqué.

---

# Sécurité

Le payload ne contient aucune donnée sensible.

Seuls les identifiants métier sont publiés.

---

# Audit

Les informations suivantes sont historisées :

- Event Name
- Enrollment ID
- Student ID
- School ID
- User ID
- Timestamp

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
EnrollmentService
        │
        ▼
Create Enrollment
        │
        ▼
Commit Transaction
        │
        ▼
EnrollmentCreated
        │
 ┌──────┼──────────────┬──────────────┐
 ▼      ▼              ▼              ▼
Attendance Finance  Timetable   Notifications
```

---

# Résumé

EnrollmentCreated est le point de départ de tous les traitements liés à une nouvelle inscription scolaire.

Il garantit :

- la synchronisation des modules dépendants ;
- une publication uniquement après validation de la transaction ;
- un découplage entre les domaines fonctionnels ;
- une architecture événementielle évolutive compatible avec les traitements asynchrones.