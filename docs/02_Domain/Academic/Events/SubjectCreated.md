# Academic Module
# Events
## SubjectCreated

Version : 1.0

---

# Objectif

SubjectCreated est déclenché lorsqu'une nouvelle matière académique est créée avec succès.

Cet événement permet aux autres modules d'être informés de l'existence d'une nouvelle discipline afin d'effectuer leurs propres traitements.

---

# Déclencheur

L'événement est publié uniquement après :

- validation des données
- création de la matière
- validation de la transaction

En cas de Rollback,

aucun événement ne doit être publié.

---

# Service émetteur

SubjectService

Méthode

create()

---

# Payload

| Champ | Type | Description |
|--------|------|-------------|
| subjectId | UUID | Identifiant de la matière |
| schoolId | UUID | École |
| code | String | Code de la matière |
| name | String | Nom de la matière |
| category | Enum | Catégorie |
| status | Enum | Statut |
| createdBy | UUID | Utilisateur |
| createdAt | Timestamp | Date de création |

---

# Consommateurs possibles

Les modules suivants peuvent écouter cet événement :

- Timetable
- Teacher Management
- Gradebook
- Examination
- Report Cards
- Academic Statistics
- Notifications
- Audit

---

# Actions possibles

Timetable

Préparer la matière pour les emplois du temps.

---

Teacher Management

Autoriser les affectations d'enseignants.

---

Gradebook

Préparer les registres de notes.

---

Examination

Autoriser la création d'évaluations.

---

Report Cards

Intégrer la matière aux bulletins.

---

Academic Statistics

Mettre à jour les indicateurs académiques.

---

Notifications

Informer les administrateurs.

---

Audit

Journaliser l'opération.

---

# Conditions de déclenchement

✓ Matière valide

✓ Code unique

✓ Transaction validée

---

# Cas où l'événement ne doit pas être publié

- Validation échouée

- Code déjà existant

- Rollback

- Exception

---

# Ordre d'exécution

Validation

↓

Création Subject

↓

Commit Transaction

↓

SubjectCreated

↓

Traitement des listeners

---

# Idempotence

Les consommateurs doivent supporter la réception multiple du même événement sans créer de doublons.

---

# Gestion des erreurs

Si un listener échoue :

- la matière reste créée ;
- les autres listeners poursuivent leur exécution ;
- l'erreur est enregistrée ;
- une stratégie de retry peut être appliquée.

---

# Sécurité

Le payload ne contient que des informations nécessaires aux consommateurs.

Aucune donnée confidentielle ne doit être publiée.

---

# Audit

Les informations suivantes sont historisées :

- Event Name
- Subject ID
- School ID
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
SubjectService
      │
      ▼
Validation
      │
      ▼
Create Subject
      │
      ▼
Commit Transaction
      │
      ▼
SubjectCreated
      │
 ┌──────┼───────────────┬──────────────┐
 ▼      ▼               ▼              ▼
Timetable Gradebook Teacher Management Notifications
```

---

# Résumé

SubjectCreated représente la création officielle d'une nouvelle matière académique.

Il garantit :

- une publication uniquement après validation de la transaction ;
- une synchronisation des modules académiques ;
- une architecture découplée basée sur les événements ;
- une intégration simple avec les futurs modules de Nexus School.