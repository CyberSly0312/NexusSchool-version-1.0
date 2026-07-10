# Academic Module
# DTOs
## CreateSubject

Version : 1.0

---

# Objectif

CreateSubject transporte toutes les données nécessaires à la création d'une nouvelle matière académique.

Il constitue le contrat entre la couche de présentation et SubjectService.

Toutes les données provenant d'une requête HTTP doivent être encapsulées dans ce DTO avant d'être transmises au service.

---

# Responsabilité

Le DTO est responsable de :

- transporter les données de création ;
- garantir une structure cohérente ;
- représenter une intention métier ;
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
| code | string | Oui | Code unique de la matière |
| name | string | Oui | Nom de la matière |
| shortName | string | Non | Nom abrégé |
| description | string | Non | Description |
| category | SubjectCategory | Oui | Catégorie |
| isOptional | boolean | Oui | Matière optionnelle |
| status | SubjectStatus | Oui | Statut |
| schoolId | UUID | Oui | École propriétaire |

---

# Contraintes

Le DTO suppose que :

- les validations FormRequest sont terminées ;
- les types de données sont corrects ;
- les champs obligatoires sont présents.

Les validations métier sont effectuées par SubjectService.

---

# Exemple

```json
{
    "code": "MAT101",
    "name": "Mathématiques",
    "shortName": "Maths",
    "description": "Mathématiques générales",
    "category": "Core",
    "isOptional": false,
    "status": "Active",
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
- SubjectService

---

# Utilisation

Controller

↓

FormRequest

↓

CreateSubject

↓

SubjectService

↓

Repository

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
CreateSubject
      │
      ▼
SubjectService
      │
      ▼
Repository
```

---

# Résumé

CreateSubject encapsule toutes les données nécessaires à la création d'une matière académique.

Il garantit une séparation claire entre la couche de présentation et la logique métier, tout en fournissant un contrat stable, cohérent et immutable.