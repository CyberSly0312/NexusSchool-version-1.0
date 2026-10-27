: les règles `maxLength(255)` sont un exemple à confirmer par rapport aux migrations et à `UserRules.php`. Si `UserResource.php` utilise déjà des règles spécifiques pour ces champs, reprends-les pour assurer la cohérence.

## Faut-il modifier d'autres fichiers ?

| Fichier                                                                  | Action                                                                                                   |
| ------------------------------------------------------------------------ | -------------------------------------------------------------------------------------------------------- |
| `app/Filament/Resources/CentralUserResource.php`                         | À modifier : ajouter les deux champs au formulaire.                                                      |
| `app/Filament/Resources/CentralUserResource/Pages/CreateCentralUser.php` | À vérifier : probablement aucune modification si Filament transmet directement les champs du formulaire. |
| `app/Domains/User/Services/UserService.php`                              | Pas nécessairement : à modifier seulement si l'on décide de raccorder la création au service métier.     |
| `app/Domains/User/DTOs/UserData.php`                                     | Pas nécessairement : vérifier que les deux noms sont déjà pris en charge.                                |
| Migrations SQL                                                           | Ne pas modifier pour masquer cette erreur.                                                               |

## Après la modification

1. Enregistre le fichier.
2. Recharge la page `/super-admin/central-users/create`.
3. Renseigne le prénom, le nom, l'adresse e-mail, le mot de passe et les autres champs requis.
4. Essaie de créer l'administrateur.

Si l'erreur persiste, il faudra vérifier la transformation des données dans `CreateCentralUser.php` et le traitement de la création. Le simple ajout des champs ne garantit pas la correction si une autre couche supprime les valeurs.

Pour que je te donne un correctif exact, adapté à ton code et prêt à copier-coller, envoie-moi le contenu actuel de `CentralUserResource.php`. Je pourrai alors te montrer précisément quoi remplacer, sans toucher au reste du