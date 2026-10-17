

cd /var/www/html/NexusSchool-version-1.0-main

echo "=== 1. IDENTITE ET GROUPES ==="
id

echo "=== 2. DOSSIER DU PROJET ==="
ls -ld /var/www/html
ls -ld .
namei -l "$PWD"

echo "=== 3. PROPRIETAIRES DES DOSSIERS IMPORTANTS ==="
ls -ld app bootstrap bootstrap/cache config public resources routes storage vendor

echo "=== 4. PERMISSIONS LARAVEL ==="
find storage bootstrap/cache -maxdepth 2 -printf '%M %u:%g %p\n' | head -n 60

echo "=== 5. FICHIERS CRITIQUES ==="
ls -l artisan .env composer.json public/index.php

echo "=== 6. TESTS DE DROITS ==="
test -r .env && echo ".env lisible par vboxuser" || echo ".env NON lisible par vboxuser"
test -w storage/logs && echo "storage/logs modifiable par vboxuser" || echo "storage/logs NON modifiable par vboxuser"
test -w bootstrap/cache && echo "bootstrap/cache modifiable par vboxuser" || echo "bootstrap/cache NON modifiable par vboxuser"
test -r vendor/autoload.php && echo "autoload Composer lisible" || echo "autoload Composer NON lisible"









vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ id
uid=1000(vboxuser) gid=1000(vboxuser) groups=1000(vboxuser),27(sudo),100(users)
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ ls -ld /v
var/         vmlinuz      vmlinuz.old  
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ ls -ld /var/www/html/
drwxr-xr-x 3 root root 4096 Oct 16 16:50 /var/www/html/
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ ls -ld .
drwxr-xr-x 19 root root 4096 Oct 17 00:17 .
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ namei -l "$PWD"
f: /var/www/html/NexusSchool-version-1.0-main
drwxr-xr-x root root /
drwxr-xr-x root root var
drwxr-xr-x root root www
drwxr-xr-x root root html
drwxr-xr-x root root NexusSchool-version-1.0-main
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ ls -ld app bootstrap/ bootstrap/cache/ config public re
reponse_Ai.md  resources/     
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ ls -ld app bootstrap/ bootstrap/cache/ config public resources/ routes/ storage/ vendor/
drwxr-xr-x 15 root     root     4096 Oct 16 16:12 app
drwxr-xr-x  3 root     root     4096 Oct 16 16:12 bootstrap/
drwxrwxr-x  2 www-data www-data 4096 Oct 17 00:15 bootstrap/cache/
drwxr-xr-x  2 root     root     4096 Oct 16 16:12 config
drwxr-xr-x  5 root     root     4096 Oct 16 16:12 public
drwxr-xr-x  5 root     root     4096 Oct 16 16:12 resources/
drwxr-xr-x  2 root     root     4096 Oct 16 16:12 routes/
drwxrwxr-x  5 www-data www-data 4096 Oct 16 16:12 storage/
drwxr-xr-x 48 root     root     4096 Oct 17 00:15 vendor/
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ find storage bootstrap/cache -maxdepth 2 -printf '%M %u:%g %p\n' | head -n 60
drwxrwxr-x www-data:www-data storage
drwxrwxr-x www-data:www-data storage/app
drwxrwxr-x www-data:www-data storage/app/public
drwxrwxr-x www-data:www-data storage/app/private
-rwxrwxr-x www-data:www-data storage/app/.gitignore
drwxrwxr-x www-data:www-data storage/logs
-rwxrwxr-x www-data:www-data storage/logs/.gitignore
-rw-rw-r-- www-data:www-data storage/logs/laravel.log
drwxrwxr-x www-data:www-data storage/framework
drwxrwxr-x www-data:www-data storage/framework/sessions
drwxrwxr-x www-data:www-data storage/framework/views
drwxrwxr-x www-data:www-data storage/framework/testing
-rwxrwxr-x www-data:www-data storage/framework/.gitignore
drwxrwxr-x www-data:www-data storage/framework/cache
drwxrwxr-x www-data:www-data bootstrap/cache
-rwxrwxr-x www-data:www-data bootstrap/cache/services.php
-rwxrwxr-x www-data:www-data bootstrap/cache/.gitignore
-rwxrwxr-x www-data:www-data bootstrap/cache/packages.php
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ ls -l artisan .env composer.json public/index.php
-rwxr-xr-x 1 root root  425 Oct 16 16:12 artisan
-rwxr-xr-x 1 root root 3135 Oct 16 16:12 composer.json
-rwxr-xr-x 1 root root 1731 Oct 17 02:07 .env
-rwxr-xr-x 1 root root  543 Oct 16 16:12 public/index.php
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ test -r .env && echo ".env lisible par vboxuser" || echo ".env NON lisible par vboxuser"
.env lisible par vboxuser
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ test -w storage/logs && echo "storage/logs modifiable par vboxuser" 
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ test -w bootstrap/cache && echo "bootstrap/cache modifiable par vboxuser" || echo "bootstrap/cache NON modifiable par vboxuser"
bootstrap/cache NON modifiable par vboxuser
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ test -r vendor/autoload.php && echo "autoload Composer lisible" || echo "autoload Composer NON lisible"
autoload Composer lisible
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$







cd /var/www/html/NexusSchool-version-1.0-main

echo "=== 1. TESTS D'ECRITURE ==="
test -w storage/logs && echo "vboxuser peut ecrire dans storage/logs" || echo "vboxuser ne peut PAS ecrire dans storage/logs"
test -w bootstrap/cache && echo "vboxuser peut ecrire dans bootstrap/cache" || echo "vboxuser ne peut PAS ecrire dans bootstrap/cache"
test -w . && echo "vboxuser peut modifier le dossier projet" || echo "vboxuser ne peut PAS modifier le dossier projet"

echo
echo "=== 2. CONFIGURATION APACHE ==="
sudo apache2ctl configtest

echo
echo "=== 3. LIEN SYMBOLIQUE STORAGE ==="
ls -ld public/storage 2>&1

echo
echo "=== 4. CONSTRUCTION FRONTEND ==="
ls -ld public/build 2>&1

echo
echo "=== 5. DERNIERES ERREURS LARAVEL ==="
sudo tail -n 60 storage/logs/laravel.log

echo
echo "=== 6. DERNIERES ERREURS APACHE ==="
sudo tail -n 40 /var/log/apache2/nexusschool-error.log

