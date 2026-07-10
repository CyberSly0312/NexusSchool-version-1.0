# Academic Module
# Permissions
## AcademicPermissions

Version : 1.0

---

# Objectif

AcademicPermissions définit les permissions nécessaires à la gestion des années académiques et des périodes académiques.

Ces permissions permettent de contrôler précisément les actions des utilisateurs selon leur rôle.

---

# Utilisation

Ces permissions sont utilisées par :

- Policies
- Middleware
- Filament Shield
- Spatie Laravel Permission
- Controllers
- Services

---

# Permissions

| Permission | Description |
|------------|-------------|
| academic.view | Consulter les années académiques |
| academic.create | Créer une année académique |
| academic.update | Modifier une année académique |
| academic.delete | Supprimer une année académique |
| academic.activate | Activer une année académique |
| academic.close | Clôturer une année académique |
| academic.archive | Archiver une année académique |
| academic.restore | Restaurer une année académique |
| academic.manage-terms | Gérer les périodes académiques |

---

# Attribution recommandée

| Rôle | Permissions |
|------|-------------|
| Super Administrator | Toutes |
| School Administrator | Toutes |
| Academic Director | Toutes sauf suppression définitive |
| Registrar | Consultation, création et modification |
| Teacher | Consultation uniquement |
| Student | Aucune |
| Parent | Aucune |

---

# Description des permissions

## academic.view

Permet de consulter :

- les années académiques
- les périodes
- les calendriers

---

## academic.create

Autorise :

- la création d'une nouvelle année académique
- la création des périodes associées

---

## academic.update

Autorise :

- la modification des informations générales
- la modification des dates
- la modification des descriptions

---

## academic.delete

Autorise :

- la suppression d'une année non utilisée.

Suppression interdite si :

- des inscriptions existent ;
- des notes existent ;
- des présences existent.

---

## academic.activate

Autorise :

- l'activation d'une année académique.

Contraintes :

- une seule année Active par école.

---

## academic.close

Autorise :

- la clôture de l'année académique ;
- le verrouillage des opérations académiques.

---

## academic.archive

Autorise :

- l'archivage définitif.

L'année devient accessible uniquement en lecture.

---

## academic.restore

Autorise :

- la restauration d'une année archivée lorsque les règles métier le permettent.

---

## academic.manage-terms

Autorise :

- créer un trimestre ou semestre ;
- modifier une période ;
- clôturer une période ;
- publier les résultats d'une période.

---

# Vérifications de sécurité

Avant toute opération, le système doit vérifier :

- que l'utilisateur est authentifié ;
- qu'il possède la permission requise ;
- qu'il appartient à la même école (School Scope) ;
- que les règles métier sont respectées.

---

# Exemple d'utilisation

```php
$user->can('academic.create');

$user->can('academic.activate');

$user->can('academic.manage-terms');
```

---

# Mapping avec les Policies

| Policy | Permission |
|---------|------------|
| viewAny | academic.view |
| view | academic.view |
| create | academic.create |
| update | academic.update |
| delete | academic.delete |
| activate | academic.activate |
| close | academic.close |
| archive | academic.archive |
| restore | academic.restore |

---

# Tests

✓ Vérification des permissions

✓ Refus des accès non autorisés

✓ Attribution correcte selon le rôle

✓ Vérification du School Scope

---

# Résumé

AcademicPermissions définit les droits nécessaires à la gestion complète des années et périodes académiques.

Il garantit un contrôle d'accès précis, sécurisé et compatible avec les Policies Laravel et Spatie Laravel Permission.