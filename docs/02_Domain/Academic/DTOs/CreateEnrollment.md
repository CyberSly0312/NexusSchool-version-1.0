# Academic Module
# DTOs
## CreateEnrollment

Version : 1.0

---

# Objectif

CreateEnrollment transporte toutes les données nécessaires à la création d'une nouvelle inscription académique.

Une Enrollment représente le dossier académique annuel d'un élève.

Toutes les données provenant de la couche de présentation doivent être encapsulées dans ce DTO avant d'être transmises à EnrollmentService.

---

# Responsabilité

Le DTO est responsable de :

- transporter les données d'inscription ;
- garantir une structure cohérente ;
- représenter une demande de création ;
- rester immutable après son instanciation.

Le DTO ne contient aucune logique métier.

---

# Source des données

Les données peuvent provenir de :

- FormRequest Laravel
- API REST
- Import Excel
- Console Commands
- Jobs
- Tests unitaires

---

# Propriétés

| Propriété | Type | Obligatoire | Description |
|-----------|------|-------------|-------------|
| studentId | UUID | Oui | Élève concerné |
| academicYearId | UUID | Oui | Année académique |
| classroomId | UUID | Oui | Classe |
| sectionId | UUID / Null | Non | Section |
| enrollmentDate | Date | Oui | Date d'inscription |
| status | EnrollmentStatus | Oui | Statut initial |
| remarks | string | Non | Observations |
| schoolId | UUID | Oui | École |

---

# Contraintes

Le DTO suppose que :

- les validations FormRequest sont terminées ;
- les UUID sont valides ;
- les formats de dates sont corrects.

Les validations métier sont réalisées exclusivement par EnrollmentService.

---

# Exemple

```json
{
    "studentId": "uuid",
    "academicYearId": "uuid",
    "classroomId": "uuid",
    "sectionId": "uuid",
    "enrollmentDate": "2026-09-15",
    "status": "Pending",
    "remarks": "Nouvelle inscription",
    "schoolId": "uuid"
}
```

---

# Immutabilité

Une fois créé,

le DTO ne peut plus être modifié.

Toute modification nécessite une nouvelle instance.

---

# Validation

Le DTO ne réalise aucune validation métier.

Les validations sont effectuées par :

- FormRequest
- Validation Rules
- EnrollmentService

---

# Utilisation

Controller

↓

FormRequest

↓

CreateEnrollment

↓

EnrollmentService

↓

Enrollment Repository

---

# Tests

✓ Création

✓ Types

✓ Immutabilité

✓ Sérialisation

✓ Désérialisation

---

# Diagramme

```
HTTP Request
        │
        ▼
FormRequest
        │
        ▼
CreateEnrollment
        │
        ▼
EnrollmentService
        │
        ▼
Repository
```

---

# Résumé

CreateEnrollment encapsule toutes les informations nécessaires à la création d'une nouvelle inscription académique.

Il garantit une séparation claire entre la couche de présentation et la logique métier, tout en fournissant une structure de données cohérente, immutable et facilement testable.