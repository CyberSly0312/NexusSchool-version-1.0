echo "=== IP TAILSCALE ==="
tailscale ip -4

echo "=== DNSMASQ ==="
sudo systemctl status dnsmasq --no-pager

echo "=== REGLE NEXUSSCHOOL ==="
sudo grep -R "nexusschool.test" /etc/dnsmasq.conf /etc/dnsmasq.d/ 2>/dev/null

echo "=== PORT DNS 53 ==="
sudo ss -lntup | grep ':53 '


vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ echo "=== IP TAILSCALE ===" tailscale ip -4
=== IP TAILSCALE === tailscale ip -4
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ echo "=== DNSMASQ ===" sudo systemctl status dnsmasq --no-pager
=== DNSMASQ === sudo systemctl status dnsmasq --no-pager
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ echo "=== REGLE NEXUSSCHOOL ===" sudo grep -R "nexusschool.test" /etc/dnsmasq.conf /etc/dnsmasq.d/ 2>/dev/null
=== REGLE NEXUSSCHOOL === sudo grep -R nexusschool.test /etc/dnsmasq.conf /etc/dnsmasq.d/
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ echo "=== PORT DNS 53 ===" sudo ss -lntup | grep ':53 '
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ 


Je vois le problème probable ! 🔎 Il y a deux points importants dans ce que tu as envoyé.

1. Dans la console Tailscale, le DNS configuré est `100.123.173.99` pour `nexusschool.test`.
2. Sur Debian, les commandes que tu as collées n'ont pas réellement été exécutées : tu as écrit `echo ...` et la commande suivante sur la même ligne, sans séparateur. Le terminal a donc simplement affiché le texte.

Nous allons vérifier l'adresse du serveur avant de modifier la configuration DNS.

## Étape 1 — Vérifier l'adresse Tailscale du serveur Debian

Exécute chaque commande séparément, puis appuie sur Entrée :

```
tailscale ip -4
```

```
sudo systemctl status dnsmasq --no-pager
```

```
sudo grep -R "nexusschool.test" /etc/dnsmasq.conf /etc/dnsmasq.d/ 2>/dev/null
```

```
sudo ss -lntup | grep ':53 '
```

Envoie-moi les résultats, même si certaines commandes n'affichent rien.

## Étape 2 — Vérifier la configuration Tailscale

Dans ta capture, l'adresse DNS est `100.123.173.99`, mais nous devons confirmer qu'elle correspond bien à l'adresse Tailscale actuelle de ton serveur Debian.

Ne change pas encore cette adresse dans la console. Si elle est incorrecte, Windows enverra ses requêtes DNS au mauvais appareil. Si elle est correcte, nous vérifierons ensuite si le serveur DNS écoute et répond effectivement.

Notre objectif est que `nexusschool.test` pointe vers l'adresse Tailscale du serveur Debian, et que le nom fonctionne uniquement pour tes appareils connectés au réseau privé Tailscale.




vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ tailscale ip -4
100.123.173.99
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo systemctl status dnsmasq --no-pager
[sudo] password for vboxuser: 
● dnsmasq.service - dnsmasq - A lightweight DHCP and caching DNS server
     Loaded: loaded (/usr/lib/systemd/system/dnsmasq.service; enabled; preset: enabled)
     Active: active (running) since Fri 2026-10-16 23:47:31 GMT; 54min ago
 Invocation: 4b8b556ca7f24abf8f048dc0b9084645
       Docs: man:dnsmasq(8)
   Main PID: 855 (dnsmasq)
      Tasks: 1 (limit: 4618)
     Memory: 360K (peak: 4.3M, swap: 468K, swap peak: 468K)
        CPU: 371ms
     CGroup: /system.slice/dnsmasq.service
             └─855 /usr/sbin/dnsmasq -x /run/dnsmasq/dnsmasq.…

Oct 16 23:47:29 debianserver systemd[1]: Starting dnsmasq.s…..
Oct 16 23:47:31 debianserver dnsmasq[855]: started, version…50
Oct 16 23:47:31 debianserver dnsmasq[855]: compile time opt…le
Oct 16 23:47:31 debianserver dnsmasq[855]: warning: no upst…ed
Oct 16 23:47:31 debianserver dnsmasq[855]: using only local…st
Oct 16 23:47:31 debianserver dnsmasq[855]: read /etc/hosts …es
Oct 16 23:47:31 debianserver systemd[1]: Started dnsmasq.se…r.
Hint: Some lines were ellipsized, use -l to show in full.
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo grep -R "nexusschool.test" /etc/dnsmasq.conf /etc/dnsmasq.d/ 2>/dev/null
/etc/dnsmasq.d/nexusschool.conf:local=/nexusschool.test/
/etc/dnsmasq.d/nexusschool.conf:address=/nexusschool.test/100.123.173.99
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo ss -lntup | grep ':53 '
udp   UNCONN 0      0                              100.123.173.99:53         0.0.0.0:*    users:(("dnsmasq",pid=855,fd=7))                                                                                                                                        
tcp   LISTEN 0      32                             100.123.173.99:53         0.0.0.0:*    users:(("dnsmasq",pid=855,fd=8))                                                                                                                                        
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ 



cd /var/www/html/NexusSchool-version-1.0-main

echo "=== ASSETS PUBLICS ==="
ls -la public
ls -la public/build 2>/dev/null

echo "=== DEPENDANCES COMPOSER ==="
test -f vendor/autoload.php && echo "vendor présent" || echo "vendor absent"

echo "=== FICHIERS FRONTEND ==="
ls -la package.json package-lock.json vite.config.* 2>/dev/null

echo "=== ROUTES FILAMENT ==="
php artisan route:list --path=super-admin



       
tcp   LISTEN 0      32                             100.123.173.99:53         0.0.0.0:*    users:(("dnsmasq",pid=855,fd=8))                                                                                                                      vboxuser@debvboxuser@debianserver:/var/www/html/NexusSchool-version-1.vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ echo "=== ASSETS PUBLICS ===" ls -la public ls -la public/build 2>/dev/null
=== ASSETS PUBLICS === ls -la public ls -la public/build
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ echo "=== DEPENDANCES COMPOSER ===" test -f vendor/autoload.php && echo "vendor présent" || echo "vendor absent"
=== DEPENDANCES COMPOSER === test -f vendor/autoload.php
vendor présent
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ echo "=== FICHIERS FRONTEND ===" ls -la package.json package-lock.json vite.config.* 2>/dev/null
=== FICHIERS FRONTEND === ls -la package.json package-lock.json vite.config.js
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ echo "=== ROUTES FILAMENT ===" php artisan route:list --path=super-admin
=== ROUTES FILAMENT === php artisan route:list --path=super-admin
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ ls -la public
total 32
drwxr-xr-x  5 root root 4096 Oct 16 16:12 .
drwxr-xr-x 19 root root 4096 Oct 17 00:17 ..
drwxr-xr-x  3 root root 4096 Oct 16 16:12 css
-rwxr-xr-x  1 root root    0 Oct 16 16:12 favicon.ico
drwxr-xr-x  3 root root 4096 Oct 16 16:12 fonts
-rwxr-xr-x  1 root root  740 Oct 16 16:12 .htaccess
-rwxr-xr-x  1 root root  543 Oct 16 16:12 index.php
drwxr-xr-x  3 root root 4096 Oct 16 16:12 js
-rwxr-xr-x  1 root root   24 Oct 16 16:12 robots.txt
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ ls -la public/build 2>/dev/null
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ test -f vendor/autoload.php && echo "vendor présent" || echo "vendor absent"
vendor présent
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ ls -la package.json package-lock.json vite.config.* 2>/dev/null
-rwxr-xr-x 1 root root   414 Oct 16 16:12 package.json
-rwxr-xr-x 1 root root 98088 Oct 16 16:12 package-lock.json
-rwxr-xr-x 1 root root   436 Oct 16 16:12 vite.config.js
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ php artisan route:list --path=super-admin

  GET|HEAD   super-admin filament.super-admin.pages.super-a…
  GET|HEAD   super-admin/audit-logs filament.super-admin.re…
  GET|HEAD   super-admin/backups filament.super-admin.resou…
  GET|HEAD   super-admin/backups/create filament.super-admi…
  GET|HEAD   super-admin/backups/{record}/edit filament.sup…
  GET|HEAD   super-admin/central-users filament.super-admin…
  GET|HEAD   super-admin/central-users/create filament.supe…
  GET|HEAD   super-admin/central-users/{record}/edit filame…
  GET|HEAD   super-admin/domains filament.super-admin.resou…
  GET|HEAD   super-admin/domains/create filament.super-admi…
  GET|HEAD   super-admin/domains/{record}/edit filament.sup…
  GET|HEAD   super-admin/login filament.super-admin.auth.lo…
  POST       super-admin/logout filament.super-admin.auth.l…
  GET|HEAD   super-admin/maintenance-windows filament.super…
  GET|HEAD   super-admin/maintenance-windows/create filamen…
  GET|HEAD   super-admin/maintenance-windows/{record}/edit …
  GET|HEAD   super-admin/notification-channels filament.sup…
  GET|HEAD   super-admin/notification-channels/create filam…
  GET|HEAD   super-admin/notification-channels/{record}/edit filament.super-admin.resources.notification-channels.edit › App\Filament\Resources\NotificationChannelResource\Pages\EditNotificationChann…
  GET|HEAD   super-admin/plans filament.super-admin.resourc…
  GET|HEAD   super-admin/plans/create filament.super-admin.…
  GET|HEAD   super-admin/plans/{record}/edit filament.super…
  GET|HEAD   super-admin/subscriptions filament.super-admin…
  GET|HEAD   super-admin/subscriptions/create filament.supe…
  GET|HEAD   super-admin/subscriptions/{record}/edit filame…
  GET|HEAD   super-admin/super-admin-schools filament.super…
  GET|HEAD   super-admin/super-admin-schools/create filamen…
  GET|HEAD   super-admin/super-admin-schools/{record}/edit …
  GET|HEAD   super-admin/system-healths filament.super-admi…
  GET|HEAD   super-admin/system-settings filament.super-adm…
  GET|HEAD   super-admin/system-settings/create filament.su…
  GET|HEAD   super-admin/system-settings/{record}/edit fila…

                                         Showing [32] routes

vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ 

cd /var/www/html/NexusSchool-version-1.0-main

echo "=== 1. ERREURS APACHE ==="
sudo tail -n 60 /var/log/apache2/nexusschool-error.log

echo "=== 2. ERREURS LARAVEL ==="
sudo tail -n 80 storage/logs/laravel.log

echo "=== 3. PERMISSIONS ==="
ls -ld storage storage/logs bootstrap/cache
ls -l storage/logs/laravel.log 2>/dev/null

echo "=== 4. ETAT DE LA CONFIGURATION LARAVEL ==="
php artisan about




vboxuser@debianserver:~$ sudo tail -n 60 /var/log/apache2/nexusschool-error.log
[sudo] password for vboxuser: 
tail: cannot open '/var/log/apache2/nexusschool-error.log' for reading: No such file or directory
vboxuser@debianserver:~$ sudo tail -n 60 /var/log/apache2/nexusschool_error.log
vboxuser@debianserver:~$ echo "=== 2. ERREURS LARAVEL ==="
=== 2. ERREURS LARAVEL ===
vboxuser@debianserver:~$ sudo tail -n 80 storage/logs/laravel.log
tail: cannot open 'storage/logs/laravel.log' for reading: No such file or directory
vboxuser@debianserver:~$ sudo tail -n 80 storage/logs/laravel.logcd ^C
vboxuser@debianserver:~$ cd /storage/logs/
bash: cd: /storage/logs/: No such file or directory
vboxuser@debianserver:~$ cd /storage/logs
bash: cd: /storage/logs: No such file or directory
vboxuser@debianserver:~$ cd /var/www/html/NexusSchool-version-1.0-main
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-mavboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo tail -n 60 /var/log/apache2/nexusschool-error.log
tail: cannot open '/var/log/apache2/nexusschool-error.log' for reading: No such file or directory
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo tail -n 80 storage/logs/laravel.log
[2026-10-17 01:44:34] production.ERROR: SQLSTATE[HY000] [1698] Access denied for user 'root'@'localhost' (Connection: mysql, Host: 127.0.0.1, Port: 3306, Database: nexus_school, SQL: select exists (select 1 from information_schema.tables where table_schema = schema() and table_name = 'migrations' and table_type in ('BASE TABLE', 'SYSTEM VERSIONED')) as `exists`) {"exception":"[object] (Illuminate\\Database\\QueryException(code: 1698): SQLSTATE[HY000] [1698] Access denied for user 'root'@'localhost' (Connection: mysql, Host: 127.0.0.1, Port: 3306, Database: nexus_school, SQL: select exists (select 1 from information_schema.tables where table_schema = schema() and table_name = 'migrations' and table_type in ('BASE TABLE', 'SYSTEM VERSIONED')) as `exists`) at /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connection.php:838)
[stacktrace]
#0 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connection.php(794): Illuminate\\Database\\Connection->runQueryCallback()
#1 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connection.php(411): Illuminate\\Database\\Connection->run()
#2 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connection.php(357): Illuminate\\Database\\Connection->select()
#3 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connection.php(374): Illuminate\\Database\\Connection->selectOne()
#4 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Schema/Builder.php(176): Illuminate\\Database\\Connection->scalar()
#5 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Migrations/DatabaseMigrationRepository.php(185): Illuminate\\Database\\Schema\\Builder->hasTable()
#6 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Migrations/Migrator.php(758): Illuminate\\Database\\Migrations\\DatabaseMigrationRepository->repositoryExists()
#7 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Console/Migrations/MigrateCommand.php(164): Illuminate\\Database\\Migrations\\Migrator->repositoryExists()
#8 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Support/helpers.php(328): Illuminate\\Database\\Console\\Migrations\\MigrateCommand->{closure:Illuminate\\Database\\Console\\Migrations\\MigrateCommand::repositoryExists():164}()
#9 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Console/Migrations/MigrateCommand.php(164): retry()
#10 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Console/Migrations/MigrateCommand.php(140): Illuminate\\Database\\Console\\Migrations\\MigrateCommand->repositoryExists()
#11 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Console/Migrations/MigrateCommand.php(110): Illuminate\\Database\\Console\\Migrations\\MigrateCommand->prepareDatabase()
#12 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Migrations/Migrator.php(671): Illuminate\\Database\\Console\\Migrations\\MigrateCommand->{closure:Illuminate\\Database\\Console\\Migrations\\MigrateCommand::runMigrations():109}()
#13 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Console/Migrations/MigrateCommand.php(109): Illuminate\\Database\\Migrations\\Migrator->usingConnection()
#14 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Console/Migrations/MigrateCommand.php(88): Illuminate\\Database\\Console\\Migrations\\MigrateCommand->runMigrations()
#15 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Database\\Console\\Migrations\\MigrateCommand->handle()
#16 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()
#17 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()
#18 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()
#19 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Container/Container.php(799): Illuminate\\Container\\BoundMethod::call()
#20 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()
#21 /var/www/html/NexusSchool-version-1.0-main/vendor/symfony/console/Command/Command.php(341): Illuminate\\Console\\Command->execute()
#22 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()
#23 /var/www/html/NexusSchool-version-1.0-main/vendor/symfony/console/Application.php(1125): Illuminate\\Console\\Command->run()
#24 /var/www/html/NexusSchool-version-1.0-main/vendor/symfony/console/Application.php(357): Symfony\\Component\\Console\\Application->doRunCommand()
#25 /var/www/html/NexusSchool-version-1.0-main/vendor/symfony/console/Application.php(196): Symfony\\Component\\Console\\Application->doRun()
#26 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(198): Symfony\\Component\\Console\\Application->run()
#27 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()
#28 /var/www/html/NexusSchool-version-1.0-main/artisan(16): Illuminate\\Foundation\\Application->handleCommand()
#29 {main}

[previous exception] [object] (PDOException(code: 1698): SQLSTATE[HY000] [1698] Access denied for user 'root'@'localhost' at /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connectors/Connector.php:67)
[stacktrace]
#0 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connectors/Connector.php(67): PDO::connect()
#1 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connectors/Connector.php(44): Illuminate\\Database\\Connectors\\Connector->createPdoConnection()
#2 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connectors/MySqlConnector.php(24): Illuminate\\Database\\Connectors\\Connector->createConnection()
#3 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connectors/ConnectionFactory.php(186): Illuminate\\Database\\Connectors\\MySqlConnector->connect()
#4 [internal function]: Illuminate\\Database\\Connectors\\ConnectionFactory->{closure:Illuminate\\Database\\Connectors\\ConnectionFactory::createPdoResolverWithHosts():181}()
#5 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connection.php(1257): call_user_func()
#6 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connection.php(1295): Illuminate\\Database\\Connection->getPdo()
#7 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connection.php(525): Illuminate\\Database\\Connection->getReadPdo()
#8 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connection.php(420): Illuminate\\Database\\Connection->getPdoForSelect()
#9 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connection.php(827): Illuminate\\Database\\Connection->{closure:Illuminate\\Database\\Connection::select():411}()
#10 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connection.php(794): Illuminate\\Database\\Connection->runQueryCallback()
#11 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connection.php(411): Illuminate\\Database\\Connection->run()
#12 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connection.php(357): Illuminate\\Database\\Connection->select()
#13 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Connection.php(374): Illuminate\\Database\\Connection->selectOne()
#14 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Schema/Builder.php(176): Illuminate\\Database\\Connection->scalar()
#15 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Migrations/DatabaseMigrationRepository.php(185): Illuminate\\Database\\Schema\\Builder->hasTable()
#16 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Migrations/Migrator.php(758): Illuminate\\Database\\Migrations\\DatabaseMigrationRepository->repositoryExists()
#17 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Console/Migrations/MigrateCommand.php(164): Illuminate\\Database\\Migrations\\Migrator->repositoryExists()
#18 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Support/helpers.php(328): Illuminate\\Database\\Console\\Migrations\\MigrateCommand->{closure:Illuminate\\Database\\Console\\Migrations\\MigrateCommand::repositoryExists():164}()
#19 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Console/Migrations/MigrateCommand.php(164): retry()
#20 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Console/Migrations/MigrateCommand.php(140): Illuminate\\Database\\Console\\Migrations\\MigrateCommand->repositoryExists()
#21 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Console/Migrations/MigrateCommand.php(110): Illuminate\\Database\\Console\\Migrations\\MigrateCommand->prepareDatabase()
#22 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Migrations/Migrator.php(671): Illuminate\\Database\\Console\\Migrations\\MigrateCommand->{closure:Illuminate\\Database\\Console\\Migrations\\MigrateCommand::runMigrations():109}()
#23 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Console/Migrations/MigrateCommand.php(109): Illuminate\\Database\\Migrations\\Migrator->usingConnection()
#24 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Database/Console/Migrations/MigrateCommand.php(88): Illuminate\\Database\\Console\\Migrations\\MigrateCommand->runMigrations()
#25 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Database\\Console\\Migrations\\MigrateCommand->handle()
#26 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()
#27 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()
#28 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()
#29 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Container/Container.php(799): Illuminate\\Container\\BoundMethod::call()
#30 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()
#31 /var/www/html/NexusSchool-version-1.0-main/vendor/symfony/console/Command/Command.php(341): Illuminate\\Console\\Command->execute()
#32 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()
#33 /var/www/html/NexusSchool-version-1.0-main/vendor/symfony/console/Application.php(1125): Illuminate\\Console\\Command->run()
#34 /var/www/html/NexusSchool-version-1.0-main/vendor/symfony/console/Application.php(357): Symfony\\Component\\Console\\Application->doRunCommand()
#35 /var/www/html/NexusSchool-version-1.0-main/vendor/symfony/console/Application.php(196): Symfony\\Component\\Console\\Application->doRun()
#36 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(198): Symfony\\Component\\Console\\Application->run()
#37 /var/www/html/NexusSchool-version-1.0-main/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()
#38 /var/www/html/NexusSchool-version-1.0-main/artisan(16): Illuminate\\Foundation\\Application->handleCommand()
#39 {main}
"} 
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ ls -ld storage storage/logs bootstrap/cache
drwxr-xr-x 2 www-data root 4096 Oct 17 00:15 bootstrap/cache
drwxr-xr-x 5 www-data root 4096 Oct 16 16:12 storage
drwxr-xr-x 2 www-data root 4096 Oct 17 01:44 storage/logs
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ ls -l storage/logs/laravel.log 2>/dev/null
-rw-r--r-- 1 root root 13257 Oct 17 01:44 storage/logs/laravel.log
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ 
echo "=== 4. ETAT DE LA CONFIGURATION LARAVEL ==="
=== 4. ETAT DE LA CONFIGURATION LARAVEL ===
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ php artisan about

  Environment ..............................................  
  Application Name ............................ Nexus School  
  Laravel Version .................................. 12.70.0  
  PHP Version ....................................... 8.4.26  
  Composer Version ................................... 2.8.8  
  Environment ................................... production  
  Debug Mode ........................................... OFF  
  URL ....................................... 100.123.173.99  
  Maintenance Mode ..................................... OFF  
  Timezone .............................. Africa/Ouagadougou  
  Locale ................................................ en  

  Cache ....................................................  
  Config ........................................ NOT CACHED  
  Events ........................................ NOT CACHED  
  Routes ........................................ NOT CACHED  
  Views ............................................. CACHED  

  Drivers ..................................................  
  Broadcasting ......................................... log  
  Cache ........................................... database  
  Database ........................................... mysql  
  Logs ...................................... stack / single  
  Mail ................................................. log  
  Queue ........................................... database  
  Session ......................................... database  

  Storage ..................................................  
  public/storage ................................ NOT LINKED  

  Filament .................................................  
  Blade Icons ................................... NOT CACHED  
  Packages  filament, forms, notifications, support, tables, actions, infolists, schemas, widgets  
  Panel Components .............................. NOT CACHED  
  Version .......................................... v5.10.2  
  Views ...................................... NOT PUBLISHED  
  Livewire .................................................  
  Livewire .......................................... v4.4.7  

  Spatie Permissions .......................................  
  Features Enabled ................................. Default  
  Version ........................................... 6.25.0  

vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ 




sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R u+rwX,g+rwX storage bootstrap/cache

