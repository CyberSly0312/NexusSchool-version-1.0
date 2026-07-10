# Academic Module
# DTOs
## UpdateAcademicYear

Version : 1.0

---

# Objectif

UpdateAcademicYear transporte les données nécessaires à la modification d'une année académique existante.

Il est utilisé exclusivement par AcademicYearService.

---

# Responsabilité

Le DTO est responsable de :

- transporter les nouvelles données
- garantir une structure cohérente
- représenter une demande de modification
- rester immutable

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
| academicYearId | UUID | Oui | Identifiant de l'année académique |
| name | string | Oui | Nom |
| code | string | Oui | Code unique |
| startDate | Date | Oui | Date de début |
| endDate | Date | Oui | Date de fin |
| description | string | Non | Description |
| status | AcademicYearStatus | Oui | Statut |

---

# Contraintes

Le DTO suppose que :

- l'année académique existe ;
- les validations de formulaire sont déjà exécutées ;
- les types de données sont valides.

Les règles métier restent de la responsabilité de AcademicYearService.

---

# Exemple

```json
{
    "academicYearId": "uuid",
    "name": "2026-2027",
    "code": "AY2026",
    "startDate": "2026-09-15",
    "endDate": "2027-07-30",
    "description": "Année académique mise à jour",
    "status": "Active"
}
```

---

# Immutabilité

Une fois créé,

le DTO ne peut plus être modifié.

Toute modification nécessite une nouvelle instance.

---

# Validation

Le DTO ne valide aucune règle métier.

Les validations sont effectuées par :

- FormRequest
- Validation Rules
- AcademicYearService

---

# Utilisation

Controller

↓

FormRequest

↓

UpdateAcademicYear

↓

AcademicYearService

---

# Tests

✓ Création

✓ Immutabilité

✓ Types

✓ Valeurs nulles

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
UpdateAcademicYear
      │
      ▼
AcademicYearService
```

---

# Résumé

UpdateAcademicYear encapsule toutes les informations nécessaires à la modification d'une année académique.

Il assure une séparation claire entre la couche de présentation et la couche métier tout en garantissant un format de données cohérent et immutable.