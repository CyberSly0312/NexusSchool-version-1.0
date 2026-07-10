# Academic Module
# DTOs
## UpdateEnrollment

Version : 1.0

---

# Objectif

UpdateEnrollment transporte les données nécessaires à la modification d'une inscription académique existante.

Il est utilisé exclusivement par EnrollmentService.

Les informations modifiables sont limitées aux champs autorisés par les règles métier.

---

# Responsabilité

Le DTO est responsable de :

- transporter les nouvelles données ;
- garantir une structure cohérente ;
- représenter une demande de mise à jour ;
- rester immutable.

Le DTO ne contient aucune logique métier.

---

# Source des données

Les données peuvent provenir de :

- FormRequest Laravel
- API REST
- Console Commands
- Jobs
- Tests unitaires

---

# Propriétés

| Propriété | Type | Obligatoire | Description |
|-----------|------|-------------|-------------|
| enrollmentId | UUID | Oui | Identifiant de l'inscription |
| classroomId | UUID | Oui | Classe actuelle |
| sectionId | UUID / Null | Non | Section |
| status | EnrollmentStatus | Oui | Statut |
| remarks | string | Non | Observations administratives |

---

# Champs non modifiables

Les propriétés suivantes ne peuvent jamais être modifiées via ce DTO :

- studentId
- academicYearId
- schoolId
- enrollmentDate

Toute tentative de modification doit être refusée par EnrollmentService.

---

# Contraintes

Le DTO suppose que :

- l'inscription existe ;
- les validations FormRequest sont terminées ;
- les types de données sont corrects.

Les validations métier sont réalisées par EnrollmentService.

---

# Exemple

```json
{
    "enrollmentId": "uuid",
    "classroomId": "uuid",
    "sectionId": "uuid",
    "status": "Active",
    "remarks": "Section modifiée par l'administration."
}
```

---

# Immutabilité

Une fois instancié,

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

UpdateEnrollment

↓

EnrollmentService

↓

Repository

---

# Tests

✓ Instanciation

✓ Immutabilité

✓ Types

✓ Champs obligatoires

✓ Sérialisation

---

# Diagramme

```
HTTP Request
        │
        ▼
FormRequest
        │
        ▼
UpdateEnrollment
        │
        ▼
EnrollmentService
        │
        ▼
Repository
```

---

# Règles métier associées

EnrollmentService doit vérifier :

- que la nouvelle classe est active ;
- que la capacité est suffisante ;
- que la section est valide ;
- que le statut demandé est autorisé ;
- qu'aucun champ protégé n'est modifié.

---

# Résumé

UpdateEnrollment encapsule les données nécessaires à la mise à jour administrative d'une inscription académique.

Il garantit une séparation claire entre la couche de présentation et la logique métier, tout en protégeant les informations immuables de l'inscription.