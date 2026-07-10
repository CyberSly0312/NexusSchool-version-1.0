# Academic Module
# Permissions
## EnrollmentPermissions

Version : 1.0

---

# Objectif

EnrollmentPermissions définit les permissions nécessaires à la gestion des inscriptions académiques des élèves.

Ces permissions contrôlent la création, la modification, le transfert, la promotion et la consultation des inscriptions.

---

# Utilisation

Ces permissions sont utilisées par :

- EnrollmentPolicy
- EnrollmentService
- Controllers
- Filament Shield
- Spatie Laravel Permission
- Workflows

---

# Permissions

| Permission | Description |
|------------|-------------|
| enrollment.view | Consulter les inscriptions |
| enrollment.create | Créer une inscription |
| enrollment.update | Modifier une inscription |
| enrollment.delete | Supprimer une inscription |
| enrollment.transfer | Transférer un élève |
| enrollment.promote | Promouvoir un élève |
| enrollment.suspend | Suspendre une inscription |
| enrollment.cancel | Annuler une inscription |
| enrollment.restore | Restaurer une inscription |

---

# Attribution recommandée

| Rôle | Permissions |
|------|-------------|
| Super Administrator | Toutes |
| School Administrator | Toutes |
| Academic Director | Toutes |
| Registrar | Consultation, création, modification, transfert |
| Teacher | Consultation uniquement |
| Student | Consultation de sa propre inscription |
| Parent | Consultation des inscriptions de ses enfants |

---

# Description des permissions

## enrollment.view

Autorise :

- consulter les inscriptions ;
- consulter l'historique académique ;
- consulter le statut d'une inscription.

---

## enrollment.create

Autorise :

- inscrire un élève ;
- créer une nouvelle inscription ;
- associer un élève à une classe.

---

## enrollment.update

Autorise :

- modifier les informations administratives ;
- mettre à jour le statut ;
- ajouter des observations.

Ne permet pas :

- de changer l'élève ;
- de changer l'année académique.

---

## enrollment.delete

Autorise :

- supprimer une inscription non utilisée.

Suppression interdite si :

- des présences existent ;
- des notes existent ;
- des paiements existent.

---

## enrollment.transfer

Autorise :

- transférer un élève vers une autre classe ;
- transférer vers une autre section ;
- transférer vers un autre programme.

Chaque transfert doit créer un historique.

---

## enrollment.promote

Autorise :

- promouvoir un élève ;
- créer une nouvelle inscription académique ;
- clôturer l'inscription précédente.

---

## enrollment.suspend

Autorise :

- suspendre temporairement une inscription.

La suspension conserve tout l'historique.

---

## enrollment.cancel

Autorise :

- annuler une inscription avant son activation.

---

## enrollment.restore

Autorise :

- restaurer une inscription lorsque les règles métier le permettent.

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
| viewAny | enrollment.view |
| view | enrollment.view |
| create | enrollment.create |
| update | enrollment.update |
| delete | enrollment.delete |
| transfer | enrollment.transfer |
| promote | enrollment.promote |
| suspend | enrollment.suspend |
| cancel | enrollment.cancel |
| restore | enrollment.restore |

---

# Exemple d'utilisation

```php
$user->can('enrollment.create');

$user->can('enrollment.transfer');

$user->can('enrollment.promote');
```

---

# Tests

✓ Vérification des permissions

✓ Vérification des rôles

✓ Refus des accès non autorisés

✓ Respect des règles métier

✓ Vérification du School Scope

---

# Résumé

EnrollmentPermissions définit les droits d'accès liés à la gestion des inscriptions académiques.

Il garantit une gestion sécurisée des opérations critiques telles que l'inscription, le transfert, la promotion et la suspension des élèves, tout en s'intégrant aux Policies Laravel et à Spatie Laravel Permission.