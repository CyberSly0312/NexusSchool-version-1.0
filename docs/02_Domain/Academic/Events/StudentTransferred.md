# Academic Module
# Events
## StudentTransferred

Version : 1.0

---

# Objectif

StudentTransferred est déclenché lorsqu'un élève est transféré vers une nouvelle structure académique.

Le transfert peut concerner :

- une classe
- une section
- une série
- un programme
- un campus
- un établissement

L'événement informe les autres modules afin qu'ils mettent à jour leurs propres données.

---

# Déclencheur

L'événement est publié uniquement après :

- validation des règles métier
- mise à jour de l'Enrollment
- validation de la transaction

En cas de Rollback,

aucun événement n'est publié.

---

# Service émetteur

EnrollmentService

Méthode

transfer()

---

# Types de transfert

Classroom

Section

Series

Program

Campus

School

---

# Payload

| Champ | Type | Description |
|--------|------|-------------|
| transferId | UUID | Identifiant du transfert |
| enrollmentId | UUID | Enrollment concernée |
| studentId | UUID | Élève |
| schoolId | UUID | École |
| transferType | Enum | Type de transfert |
| previousClassroomId | UUID / Null | Ancienne classe |
| newClassroomId | UUID / Null | Nouvelle classe |
| previousSectionId | UUID / Null | Ancienne section |
| newSectionId | UUID / Null | Nouvelle section |
| previousSeriesId | UUID / Null | Ancienne série |
| newSeriesId | UUID / Null | Nouvelle série |
| transferredBy | UUID | Utilisateur |
| transferredAt | Timestamp | Date |

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
- Parent Portal
- Notifications
- Audit

---

# Actions possibles

Attendance

Changer le registre de présence.

---

Timetable

Associer le nouvel emploi du temps.

---

Finance

Recalculer les frais si nécessaire.

---

Library

Mettre à jour l'affectation.

---

Transport

Modifier l'itinéraire.

---

Notifications

Informer les parents.

---

Audit

Historiser le transfert.

---

# Conditions de déclenchement

✓ Enrollment valide

✓ Destination valide

✓ Capacité disponible

✓ Validation métier réussie

✓ Transaction validée

---

# Cas où l'événement ne doit pas être publié

- Classe complète

- Série invalide

- Programme incompatible

- Rollback

- Exception

---

# Ordre d'exécution

Validation

↓

Contrôle capacité

↓

Mise à jour Enrollment

↓

Commit

↓

StudentTransferred

↓

Traitement des listeners

---

# Idempotence

Chaque listener doit être capable de traiter plusieurs fois le même événement sans provoquer de doublons.

---

# Gestion des erreurs

Si un listener échoue :

- le transfert reste valide ;

- les autres listeners continuent ;

- l'erreur est journalisée ;

- un retry peut être exécuté.

---

# Sécurité

Le payload contient uniquement des identifiants métier.

Aucune donnée personnelle n'est publiée.

---

# Audit

Les informations suivantes sont historisées :

- Event Name
- Transfer ID
- Student ID
- Enrollment ID
- User ID
- Timestamp
- Tenant ID

---

# Tests

✓ Publication après Commit

✓ Rollback

✓ Payload complet

✓ Payload valide

✓ Validation multi-tenant

✓ Retry

---

# Diagramme

```
EnrollmentService
        │
        ▼
Validation
        │
        ▼
Transfer Enrollment
        │
        ▼
Commit
        │
        ▼
StudentTransferred
        │
 ┌──────┼───────────────┬──────────────┐
 ▼      ▼               ▼              ▼
Attendance Timetable Finance Notifications
```

---

# Résumé

StudentTransferred représente tout changement de structure académique d'un élève.

Il garantit :

- la cohérence entre les modules ;
- la mise à jour des affectations académiques ;
- la conservation de l'historique des transferts ;
- une synchronisation fiable via une architecture orientée événements.