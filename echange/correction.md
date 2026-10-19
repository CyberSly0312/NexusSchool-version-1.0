
# NexusSchool — Fix Central User Creation: Missing `first_name` and `last_name`

You are working directly inside the existing NexusSchool ERP codebase on Debian.

## Environment

- Laravel 12.70.0
- PHP 8.4.26
- Filament 5.10.2
- Livewire 4.4.7
- MariaDB/MySQL
- Database: `Nexusschool`
- Current route: `/super-admin/central-users/create`
- Current hostname: `www.nexusschool.test`

## Reported error

When creating a school administrator for testing through the Super Admin interface, Laravel throws:

`Illuminate\Database\QueryException: SQLSTATE[HY000]: General error: 1364 Field 'first_name' doesn't have a default value`

The failed SQL statement is equivalent to:

```sql
INSERT INTO users (
    email,
    phone,
    school_id,
    password,
    uuid,
    updated_at,
    created_at
) VALUES (...);
```

The SQL does not include `first_name` or `last_name`, even though the database requires `first_name`.

The failure occurs during Filament's `CreateRecord` process.

## Objective

Identify and fix the actual root cause so that a Super Admin can create a school administrator successfully, while preserving NexusSchool's existing architecture and business rules.

## Mandatory investigation

1. Inspect the existing Central Users Filament resource, its Create page, form schema, and any related actions or services.
2. Trace the complete user-creation flow from the submitted Filament form through validation, data transformation, service calls, and Eloquent model persistence.
3. Inspect the `User` model, its fillable/guarded attributes, casts, observers, model events, and any creation services or actions.
4. Inspect the users table migration history and current schema, particularly `first_name`, `last_name`, `email`, `school_id`, `phone`, `uuid`, and `password`.
5. Compare this flow with the existing school-administrator creation flow elsewhere in the application, if one exists.
6. Determine whether the fields are missing from the form, omitted during data transformation, discarded by mass assignment, or lost when calling a service or creating the model.
7. Check whether the current implementation uses a canonical user-creation service or other architectural contract that must be reused.

Do not assume that adding fields to `$fillable` alone will solve the problem. Verify the actual cause first.

## Implementation requirements

- Ensure that `first_name` and `last_name` are collected and validated wherever required by the existing domain rules.
- Ensure both values reach the persistence layer and are stored correctly.
- If the Central Users form already collects these values, fix the transformation or persistence layer that drops them.
- If the form is missing them, add the appropriate fields using the existing Filament conventions, labels, validation rules, and layout.
- Preserve password hashing, UUID generation, role assignment, school association, tenant isolation, and authorization behavior.
- Respect the distinction between central users and school-scoped users. Do not remove or weaken existing authorization or tenant-isolation protections.
- Follow the project's existing DDD/Clean Architecture boundaries and reuse the established user-creation service or action where appropriate.
- Check whether other creation flows share the same defect. Fix the shared root cause if justified, without introducing unrelated refactoring.
- Do not make `first_name` or `last_name` nullable, add fake default values, or modify database constraints merely to suppress this exception.
- Do not delete data, reset the database, run fresh migrations, or use destructive migration commands.
- Do not modify `.env` or unrelated DNS, Apache, tenancy, or deployment configuration.

## Tests

Add or update focused automated tests using the project's existing Pest conventions.

At minimum, verify that:

1. A valid central user can be created with first name, last name, email, and the appropriate school association.
2. The persisted record contains the correct `first_name` and `last_name`.
3. Password hashing, UUID generation, and role assignment still work as expected.
4. Invalid or missing required names are rejected through validation.
5. Existing authorization and school/tenant isolation rules remain enforced.
6. Existing relevant tests continue to pass.

Use existing factories, test helpers, and database setup. Do not weaken assertions or tests just to obtain a passing result.

## Verification and reporting

After implementing the fix:

1. Run the focused tests.
2. Run the relevant additional tests and the project's available static analysis or code-quality checks, where practical.
3. Review the final diff and ensure no unrelated files or behavior were changed.
4. Do not claim that a test passed unless it was actually executed and passed.

Report:
- The confirmed root cause.
- The exact files changed and why.
- The fix implemented.
- The tests executed and their actual results.
- Any remaining issue or follow-up needed.

## Working-tree safety

This is an existing project with potentially important pre-existing modifications.

Before editing:
- Inspect `git status --short`.
- Do not reset, stash, discard, overwrite, or revert pre-existing changes.
- Do not revert unrelated work.
- Keep the patch minimal and consistent with existing project conventions.

Implement the fix, not just a diagnosis. Stop and report if you discover a conflicting architectural decision or a change that would require a destructive database operation.






vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo apache2ctl configtest && sudo systemctl reload apa
che2
Syntax OK
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo chown -R www-data:www-data storage bootstrap/cache
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo chmod -R ug+rwX storage bootstrap/cache
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo apache2ctl -S
[sudo] password for vboxuser: 
VirtualHost configuration:
*:80                   debianserver.myguest.virtualbox.org (/etc/apache2/sites-enabled/000-default.conf:1)
ServerRoot: "/etc/apache2"
Main DocumentRoot: "/var/www/html"
Main ErrorLog: "/var/log/apache2/error.log"
Mutex watchdog-callback: using_defaults
Mutex rewrite-map: using_defaults
Mutex default: dir="/var/run/apache2/" mechanism=default 
Mutex mpm-accept: using_defaults
PidFile: "/var/run/apache2/apache2.pid"
Define: DUMP_VHOSTS
Define: DUMP_RUN_CFG
User: name="www-data" id=33
Group: name="www-data" id=33
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ grep -n -E "DocumentRoot|<Directory" /etc/apache2/sites-enabled/*.conf
12:     DocumentRoot /var/www/html
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ ls -ld /var/www/html/NexusSchool-version-1.0-main/public
drwxr-xr-x 5 root root 4096 Oct 16 16:12 /var/www/html/NexusSchool-version-1.0-main/public
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ ls /var/www/html/NexusSchool-version-1.0-main/public/index.php
/var/www/html/NexusSchool-version-1.0-main/public/index.php
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo tail -n 15 /var/log/apache2/nexusschool_error.log
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ 







`apache2ctl -S` montre qu'une seule configuration est active : `000-default.conf`, avec `DocumentRoot /var/www/html`. Ton VirtualHost `nexusschool` n'est donc pas activé, et c'est pour cela que le projet est encore servi depuis son dossier parent. Aucune requête ne passe par `public/`. Le `configtest` « Syntax OK » ne prouvait rien : il valide la syntaxe des sites actifs, pas leur existence.

Le chemin est maintenant connu : `/var/www/html/NexusSchool-version-1.0-main/public`. `index.php` y est bien et les permissions de lecture suffisent. Ton `tail` portait sur `nexusschool_error.log` (avec un tiret bas), alors que mon fichier utilise `nexusschool-error.log` (avec un tiret). Tu as peut-être créé ta propre configuration avec un autre nom, donc regarde d'abord ce qui existe :

```
ls -l /etc/apache2/sites-available/ /etc/apache2/sites-enabled/
```

**Si tu vois un fichier `nexusschool*.conf` dans `sites-available`**, vérifie son contenu (`cat`) : le `DocumentRoot` doit se terminer par `/public`. Active-le avec son vrai nom.

**S'il n'existe pas**, crée-le avec le chemin réel :

```
sudo a2enmod rewrite
sudo tee /etc/apache2/sites-available/nexusschool.conf <<'EOF'
<VirtualHost *:80>
    ServerName nexusschool.test
    ServerAlias *.nexusschool.test
    DocumentRoot /var/www/html/NexusSchool-version-1.0-main/public

    <Directory /var/www/html/NexusSchool-version-1.0-main/public>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/nexusschool-error.log
    CustomLog ${APACHE_LOG_DIR}/nexusschool-access.log combined
</VirtualHost>
EOF
```

Dans les deux cas, active le site, désactive celui par défaut, puis recharge :

```
sudo a2ensite nexusschool
sudo a2dissite 000-default
sudo apache2ctl configtest && sudo systemctl reload apache2
sudo apache2ctl -S
```

Cette fois `apache2ctl -S` doit afficher `nexusschool.test` avec le bon fichier. Si `a2ensite` répond « does not exist », le nom du fichier est différent : prends celui que `ls` t'a montré.

Ensuite, teste `http://nexusschool.test/super-admin` et `http://100.123.173.99/`. Si le résultat n'est pas bon, envoie-moi :

```
sudo tail -n 20 /var/log/apache2/nexusschool-error.log
sudo tail -n 20 /var/www/html/NexusSchool-version-1.0-main/storage/logs/laravel.log
ls -l /var/www/html/NexusSchool-version-1.0-main/.env
```

Le dernier fichier (le `.env`) doit exister et être lisible par `www-data`. S'il est en `600` et appartient à `vboxuser`, Laravel ne pourra pas le lire, et il faudra ajuster le propriétaire ou le groupe.

**Rappel de sécurité :** tant que `000-default` reste actif, `http://nexusschool.test/NexusSchool-version-1.0-main/.env` est téléchargeable par n'importe quel appareil du tailnet. Un `curl -I` sur cette URL te dira si c'est le cas. Dès que le nouveau vhost est actif, cette URL doit renvoyer 404. Si elle a répondu `200` à un moment, change `APP_KEY` et le mot de passe de la base de données.
