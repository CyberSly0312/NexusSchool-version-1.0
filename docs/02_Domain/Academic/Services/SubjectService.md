# Academic Module
# Events
## AcademicYearCreated

Version : 1.0

---

# Objectif

AcademicYearCreated est déclenché lorsqu'une nouvelle année académique est créée avec succès.

Cet événement permet aux autres modules du système d'être informés sans créer de dépendances directes avec AcademicYearService.

---

# Déclencheur

L'événement est émis uniquement après :

- validation des données
- sauvegarde en base de données
- validation de la transaction

Si la transaction échoue,

aucun événement ne doit être publié.

---

# Service émetteur

AcademicYearService

Méthode

create()

---

# Payload

| Champ | Type | Description |
|--------|------|-------------|
| academicYearId | UUID | Identifiant de l'année académique |
| schoolId | UUID | École propriétaire |
| name | String | Nom de l'année académique |
| startDate | Date | Date de début |
| endDate | Date | Date de fin |
| status | Enum | Statut initial |
| createdBy | UUID | Utilisateur créateur |
| createdAt | Timestamp | Date de création |

---

# Consommateurs possibles

Les modules suivants peuvent écouter cet événement :

- Timetable
- Enrollment
- Finance
- Notifications
- Audit
- Reporting

---

# Actions possibles

Le module Timetable peut :

- préparer une nouvelle planification.

Le module Enrollment peut :

- ouvrir les inscriptions.

Le module Finance peut :

- préparer une nouvelle période de facturation.

Le module Notification peut :

- informer les administrateurs.

Le module Audit :

- journalise l'opération.

---

# Conditions de déclenchement

✓ Transaction validée

✓ Données persistées

✓ Validation métier réussie

---

# Cas où l'événement ne doit pas être déclenché

- Échec de validation
- Rollback
- Exception
- Violation des règles métier

---

# Ordre d'exécution

Validation

↓

Transaction

↓

Création AcademicYear

↓

Commit

↓

Publication de l'événement

↓

Traitement des listeners

---

# Idempotence

Les consommateurs doivent pouvoir traiter plusieurs fois le même événement sans produire d'effets secondaires.

---

# Gestion des erreurs

Si un listener échoue :

- l'année académique reste créée ;
- les autres listeners continuent leur exécution ;
- l'erreur est journalisée ;
- une stratégie de retry peut être appliquée.

---

# Sécurité

Le payload ne doit contenir aucune donnée sensible.

Les informations publiées doivent être limitées aux besoins des consommateurs.

---

# Audit

Les informations suivantes sont enregistrées :

- Event Name
- Aggregate ID
- User ID
- Timestamp
- Tenant ID

---

# Tests

✓ Événement publié après création

✓ Aucun événement en cas de rollback

✓ Payload complet

✓ Payload valide

✓ Multi-tenant

✓ Retry des listeners

---

# Diagramme

```
AcademicYearService
        │
        ▼
Create AcademicYear
        │
        ▼
Commit Transaction
        │
        ▼
AcademicYearCreated
        │
 ┌──────┼───────────────┐
 ▼      ▼               ▼
Audit Notifications Finance
```

---

# Résumé

AcademicYearCreated est le point d'entrée des traitements déclenchés après la création d'une année académique.

Il garantit :

- une publication uniquement après validation de la transaction ;
- un découplage entre les modules ;
- une propagation sécurisée de l'information ;
- une architecture orientée événements compatible avec une évolution vers des files de messages (RabbitMQ, Kafka, SQS, etc.).