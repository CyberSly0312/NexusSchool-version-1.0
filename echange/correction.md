
- Relis les fichiers concernés pour confirmer que le diagnostic est toujours valable.

Correctif attendu :
1. Remplacer le champ générique `name` par les champs appropriés `first_name` et `last_name`, en respectant les conventions de formulaire existantes.
2. Vérifier la validation et la persistance des deux champs.
3. Vérifier si la création d'un utilisateur central doit passer par `UserService` ou une action métier existante. Réutiliser cette logique si cela respecte l'architecture du projet, sans introduire de refactorisation générale.
4. Préserver la génération UUID, le hachage du mot de passe, l'association scolaire, les rôles, les autorisations et l'isolation des tenants.
5. Ajouter ou adapter des tests ciblés pour la création d'un utilisateur central et la persistance des noms.
6. Exécuter les tests pertinents et rapporter leurs résultats réels.
7. Examiner le diff final et ne modifier aucun fichier sans rapport avec ce défaut.

Ne modifie pas le schéma de la base de données pour masquer l'erreur. N'exécute aucune migration destructive et ne touche pas au fichier `.env`.

Implémente le correctif, puis fournis le diagnostic final, les fichiers modifiés et les résultats des tests.






Je ne peux pas appliquer le correctif dans cette session : malgré les confirmations, les permissions restent inchangées. getfacl ne montre aucune ACL pour vboxuser, et les vérifications d’écriture échouent sur le formulaire, la page de création et le répertoire de tests. Aucun fichier n’a été modifié et aucun test n’a été exécuté.

Le diagnostic reste confirmé : CentralUserResource.php collecte name, mais la table exige first_name et last_name. CreateCentralUser.php utilise la création Filament générique. Le UserService.php crée l’utilisateur à partir de UserData, déclenche l’événement métier et s’appuie sur le modèle pour l’UUID ; si ce service est raccordé, il faudra aussi éviter de hacher le mot de passe une deuxième fois. La validation des noms est déjà définie dans UserRules.php.

L’état Git conserve les fichiers préexistants non suivis ; je n’y ai apporté aucun changement. Une fois les ACL réellement visibles dans getfacl pour les chemins concernés, le correctif et les tests ciblés pourront être réalisés.



`apache2ctl -S` montre qu'une seule configuration est active : `000-default.conf`, avec `DocumentRoot /var/www/html`. Ton VirtualHost `nexusschool` n'est donc pas activé, et c'est pour cela que le projet est encore servi depuis son dossier parent. Aucune requête ne passe par `public/`. Le `configtest` « Syntax OK » ne prouvait rien : il valide la syntaxe des sites actifs, pas leur existence.

Le chemin est maintenant connu : `/var/www/html/NexusSchool-version-1.0-main/public`. `index.php` y est bien et les permissions de lecture suffisent. Ton `tail` portait sur `nexusschool_error.log` (avec un tiret bas), alors que mon fichier utilise `nexusschool-error.log` (avec un tiret). Tu as peut-être créé ta propre configuration avec un autre nom, donc regarde d'abord ce qui existe :











Oui. Puisque le diagnostic est déjà établi, tu peux commencer par modifier un seul fichier principal :

`app/Filament/Resources/CentralUserResource.php`

C'est le formulaire de création des utilisateurs centraux qui collecte actuellement `name`, alors que la table `users` attend `first_name` et `last_name`.

Le correctif consiste à remplacer le champ `name` par deux champs distincts, en conservant les règles de validation existantes. Il n'est pas nécessaire de modifier la base de données pour résoudre cette erreur.

## Modification à effectuer

Dans ce fichier, recherche le champ :

```
TextInput::make('name')
```

Remplace-le par deux champs :

```
TextInput::make('first_name')
    ->label('First name')
    ->required()
    ->maxLength(255),

TextInput::make('last_name')
    ->label('Last name')
    ->required()
    ->maxLength(255),
```

Ces champs doivent être placés dans le schéma du formulaire, à l'endroit où se trouvait le champ `name`.

Attention : les règles `maxLength(255)` sont un exemple à confirmer par rapport aux migrations et à `UserRules.php`. Si `UserResource.php` utilise déjà des règles spécifiques pour ces champs, reprends-les pour assurer la cohérence.

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

Pour que je te donne un correctif exact, adapté à ton code et prêt à copier-coller, envoie-moi le contenu actuel de `CentralUserResource.php`. Je pourrai alors te montrer précisément quoi remplacer, sans toucher au reste du fichier.
