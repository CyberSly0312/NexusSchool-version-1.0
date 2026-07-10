# Academic Module
# DTOs
## UpdateSubject

Version : 1.0

---

# Objectif

UpdateSubject transporte les données nécessaires à la modification d'une matière académique existante.

Il constitue le contrat entre la couche de présentation et SubjectService.

Les validations métier restent sous la responsabilité du service.

---

# Responsabilité

Le DTO est responsable de :

- transporter les nouvelles données ;
- garantir une structure cohérente ;
- représenter une demande de modification ;
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
| subjectId | UUID | Oui | Identifiant de la matière |
| code | string | Oui | Code de la matière |
| name | string | Oui | Nom de la matière |
| shortName | string | Non | Nom abrégé |
| description | string | Non | Description |
| category | SubjectCategory | Oui | Catégorie |
| isOptional | boolean | Oui | Matière optionnelle |
| status | SubjectStatus | Oui | Statut |

---

# Champs protégés

Les propriétés suivantes ne doivent jamais être modifiées directement :

- schoolId
- createdAt
- createdBy

Toute modification nécessite un traitement spécifique ou doit être refusée.

---

# Contraintes

Le DTO suppose que :

- la matière existe ;
- les validations FormRequest sont terminées ;
- les types de données sont corrects.

Les validations métier sont effectuées par SubjectService.

---

# Exemple

```json
{
    "subjectId": "uuid",
    "code": "MAT101",
    "name": "Mathématiques",
    "shortName": "Maths",
    "description": "Mathématiques générales",
    "category": "Core",
    "isOptional": false,
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

UpdateSubject

↓

SubjectService

↓

Repository

---

# Tests

✓ Instanciation

✓ Immutabilité

✓ Types

✓ Champs obligatoires

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
UpdateSubject
      │
      ▼
SubjectService
      │
      ▼
Repository
```

---

# Règles métier associées

SubjectService doit vérifier :

- que le code reste unique ;
- que la catégorie est valide ;
- que la matière n'est pas archivée si elle est utilisée ;
- que les modifications n'impactent pas l'historique académique.

---

# Résumé

UpdateSubject encapsule les données nécessaires à la mise à jour d'une matière académique.

Il fournit un contrat clair entre la couche de présentation et la couche métier, tout en garantissant une structure de données cohérente, immutable et facilement testable.