

vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sed -n '180,245p' app/Domains/School/Services/SchoolService.php
        }

        if (isset($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    private function canonicalSlug(string $value): string
    {
        $slug = Str::lower(Str::slug($value));

        if ($slug === '' || strlen($slug) > 63 || ! preg_match('/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/', $slug)) {
            throw ValidationException::withMessages([
                'slug' => 'The school slug must produce a valid DNS subdomain (letters, numbers, and internal hyphens only).',
            ]);
        }

        if (in_array($slug, config('tenancy.reserved_subdomains', []), true)) {
            throw ValidationException::withMessages([
                'slug' => "The subdomain '{$slug}' is reserved by the platform.",
            ]);
        }

        return $slug;
    }

    private function canonicalDomain(string $slug): string
    {
        $baseDomain = Str::lower(trim((string) config('tenancy.tenant_base_domain')));

        if (! preg_match('/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/', $baseDomain)) {
            throw new \LogicException('TENANT_BASE_DOMAIN must be a valid DNS domain.');
        }

        return "{$slug}.{$baseDomain}";
    }

    private function isUniqueConstraintViolation(QueryException $exception): bool
    {
        return in_array((string) $exception->getCode(), ['23000', '23505', '19'], true);
    }
}
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ 





vboxuser@debianserver:~$ sudo apt install dnsmasq
[sudo] password for vboxuser: 
dnsmasq is already the newest version (2.91-1+deb13u2).
The following package was automatically installed and is no longer required:
  linux-image-6.12.107+deb13-amd64
Use 'sudo apt autoremove' to remove it.

Summary:
  Upgrading: 0, Installing: 0, Removing: 0, Not Upgrading: 0
vboxuser@debianserver:~$ sudo apt autoremove
REMOVING:                       
  linux-image-6.12.107+deb13-amd64

Summary:
  Upgrading: 0, Installing: 0, Removing: 1, Not Upgrading: 0
  Freed space: 111 MB

Continue? [Y/n] y
(Reading database ... 148132 files and directories currently installed.)
Removing linux-image-6.12.107+deb13-amd64 (6.12.107-1) ...
/etc/kernel/postrm.d/initramfs-tools:
update-initramfs: Deleting /boot/initrd.img-6.12.107+deb13-amd64
/etc/kernel/postrm.d/zz-update-grub:
Generating grub configuration file ...




Ce comportement est peut-être normal : avec le nouveau VirtualHost, l'ancienne URL ne doit plus fonctionner.

## Pourquoi l'ancienne URL casse

Avant, Apache servait un dossier parent, et tu entrais dans le projet par `/NexusSchool-version-1.0-main/public/`. Maintenant la racine d'Apache **est** `public/`, donc ce même chemin est cherché *à l'intérieur* de `public/` (`public/NexusSchool-version-1.0-main/public/`), qui n'existe pas. Les nouvelles adresses sont :

- `http://nexusschool.test/` et `http://nexusschool.test/super-admin`
- `http://100.123.173.99/` aussi, puisque `000-default` est désactivé et que ton vhost reçoit donc les requêtes par IP.

Teste ces trois adresses d'abord. Si elles marchent, il n'y a rien à réparer, tu utilises simplement les nouvelles URL.

## Si ça ne marche pas non plus

Vérifie d'abord que tu as bien remplacé `/CHEMIN` dans le fichier de configuration. Si tu l'as collé tel quel, Apache pointe vers un dossier qui n'existe pas et tout renvoie une erreur (la vérification `configtest` ne bloque pas dans ce cas). Je ne connais pas le chemin réel de ton projet : d'après ta première capture, il est probablement sous `/var/www/html/`, mais c'est une supposition à confirmer. Lance ceci sur le serveur et colle-moi la sortie :

```
sudo apache2ctl -S
grep -n -E "DocumentRoot|<Directory" /etc/apache2/sites-enabled/*.conf
ls -ld /var/www/html/NexusSchool-version-1.0-main/public
ls /var/www/html/NexusSchool-version-1.0-main/public/index.php
sudo tail -n 15 /var/log/apache2/nexusschool-error.log
```

Selon le résultat :

| Symptôme | Cause probable |
|---|---|
| 404 d'Apache | `DocumentRoot` faux (placeholder `/CHEMIN` ou mauvais dossier) |
| 403 Forbidden | Permissions : `www-data` doit pouvoir traverser chaque dossier parent du projet. Un projet sous `/home/vboxuser` pose souvent ce problème. |
| Page blanche ou erreur 500 de Laravel | Permissions sur `storage` et `bootstrap/cache`, ou `.env` manquant |
| Affichage sans style, Livewire qui ne répond pas | `APP_URL` encore sur l'ancien sous-chemin. Mets `APP_URL=http://nexusschool.test`, puis `php artisan config:clear`. |

## Retour arrière temporaire

Si tu as besoin de l'accès en urgence, tu peux revenir à l'ancien état :

```
sudo a2dissite nexusschool && sudo a2ensite 000-default && sudo systemctl reload apache2
```

Mais l'ancien état expose tout le projet (y compris `.env`) dans le navigateur. Fais-le seulement le temps de me renvoyer les diagnostics ci-dessus, et n'oublie pas de réactiver `nexusschool` ensuite.




sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R ug+rwX storage bootstrap/cache











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
