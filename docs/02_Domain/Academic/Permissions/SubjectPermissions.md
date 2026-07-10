# Academic Module
# Permissions
## SubjectPermissions

Version : 1.0

---

# Objectif

SubjectPermissions définit les permissions nécessaires à la gestion des matières académiques.

Ces permissions permettent de contrôler la création, la modification, l'archivage et la consultation des matières selon le rôle de chaque utilisateur.

---

# Utilisation

Ces permissions sont utilisées par :

- SubjectPolicy
- SubjectService
- Controllers
- Filament Shield
- Spatie Laravel Permission
- Validation Rules

---

# Permissions

| Permission | Description |
|------------|-------------|
| subject.view | Consulter les matières |
| subject.create | Créer une matière |
| subject.update | Modifier une matière |
| subject.delete | Supprimer une matière |
| subject.archive | Archiver une matière |
| subject.restore | Restaurer une matière |
| subject.assign-teachers | Assigner des enseignants à une matière |

---

# Attribution recommandée

| Rôle | Permissions |
|------|-------------|
| Super Administrator | Toutes |
| School Administrator | Toutes |
| Academic Director | Toutes |
| Registrar | Consultation uniquement |
| Head of Department | Consultation, création, modification |
| Teacher | Consultation |
| Student | Consultation |
| Parent | Consultation |

---

# Description des permissions

## subject.view

Autorise :

- consulter la liste des matières ;
- consulter les détails d'une matière ;
- consulter les coefficients associés.

---

## subject.create

Autorise :

- créer une nouvelle matière ;
- définir son type ;
- définir son coefficient initial.

---

## subject.update

Autorise :

- modifier les informations générales ;
- modifier la description ;
- modifier le statut.

Ne permet pas :

- de modifier l'historique académique ;
- d'altérer les résultats déjà publiés.

---

## subject.delete

Autorise :

- supprimer une matière non utilisée.

Suppression interdite si :

- la matière est utilisée dans une classe ;
- des notes existent ;
- des évaluations existent ;
- des emplois du temps existent.

---

## subject.archive

Autorise :

- archiver une matière.

Une matière archivée devient indisponible pour les nouvelles affectations mais reste consultable dans l'historique.

---

## subject.restore

Autorise :

- restaurer une matière archivée.

La restauration est autorisée uniquement si les règles métier sont respectées.

---

## subject.assign-teachers

Autorise :

- affecter un ou plusieurs enseignants à une matière ;
- modifier les affectations existantes ;
- retirer une affectation.

Toutes les affectations doivent être historisées.

---

# Vérifications de sécurité

Avant toute opération, le système doit vérifier :

- que l'utilisateur est authentifié ;
- qu'il possède la permission requise ;
- qu'il appartient à la même école ;
- que les règles métier sont respectées.

---

# Mapping avec les Policies

| Policy | Permission |
|---------|------------|
| viewAny | subject.view |
| view | subject.view |
| create | subject.create |
| update | subject.update |
| delete | subject.delete |
| archive | subject.archive |
| restore | subject.restore |
| assignTeacher | subject.assign-teachers |

---

# Exemple d'utilisation

```php
$user->can('subject.view');

$user->can('subject.create');

$user->can('subject.assign-teachers');
```

---

# Tests

✓ Vérification des permissions

✓ Vérification des rôles

✓ Refus des accès non autorisés

✓ Vérification des règles métier

✓ Vérification du School Scope

---

# Résumé

SubjectPermissions définit les droits d'accès liés à la gestion des matières académiques.

Il garantit un contrôle d'accès précis pour toutes les opérations de création, modification, archivage et affectation des enseignants, tout en restant compatible avec les Policies Laravel et Spatie Laravel Permission.