Je poursuis l'audit. D'abord l'inventaire complet des ADR et du Shared kernel.
$ Get-ChildItem docs\ADR -File | ForEach-Object { $l1 = (Get-Content $_.FullName -TotalCount 4 | Where-Object { $_ -match '\S' } | Select-Object -First 2) -join ' | '; "$($_.Name): $l1" }
ADR-001.md: 
ADR-002.md: 
ADR-003.md: 
ADR-004.md: 
ADR-005.md: 
ADR-006.md: # ADR-006 | # Staff Workforce Architecture
ADR-007.md: 
ADR-008.md: 
ADR-009.md: 
ADR-010.md: 
…
Click to expand
$ Write-Output "---SHARED/TRAITS---"; Get-ChildItem app\Domains\Shared\Traits -File | Select-Object Name; Write-Output "---SHARED/ENUMS---"; Get-ChildItem app\Domains\Shared\Enums -File | Select-Object Name
---SHARED/TRAITS---

Name                         
----                         
HasEntityExtraction.php      
UniqueValidator.php          
---SHARED/ENUMS---
ActiveArchivedStatus.php     
DraftActiveArchivedStatus.php
+ Thought: 2.4s
$ Get-ChildItem docs\ADR -File | ForEach-Object { $t = Select-String -Path $_.FullName -Pattern "^# [A-Z]" | Where-Object { $_.Line -notmatch '^# ADR' } | Select-Object -First 1; "$($_.Name): $($t.Line)" }
ADR-001.md: 
ADR-002.md: 
ADR-003.md: 
ADR-004.md: 
ADR-005.md: 
ADR-006.md: # Staff Workforce Architecture
ADR-007.md: 
ADR-008.md: 
ADR-009.md: 
ADR-010.md: 
…
Click to expand
$ foreach ($f in 'app\Domains\Shared\Enums\ActiveArchivedStatus.php','app\Domains\Shared\Enums\DraftActiveArchivedStatus.php') { Write-Output "=== $f ==="; Get-Content $f }
=== app\Domains\Shared\Enums\ActiveArchivedStatus.php ===
<?php

declare(strict_types=1);

namespace App\Domains\Shared\Enums;

enum ActiveArchivedStatus: string
{
    case Active = 'active';
…
Click to expand
+ Thought: 2.0s
$ Get-ChildItem docs\ADR -File | ForEach-Object { $lines = Get-Content $_.FullName -TotalCount 6; "$($_.Name) :: $($lines[1])" }
Cannot index into a null array.
At line:1 char:114
+ ... Get-Content $_.FullName -TotalCount 6; "$($_.Name) :: $($lines[1])" }
+                                                             ~~~~~~~~~
    + CategoryInfo          : InvalidOperation: (:) [], RuntimeException
    + FullyQualifiedErrorId : NullArray
 
ADR-001.md :: 
Cannot index into a null array.
At line:1 char:114
…
Click to expand
+ Thought: 5.3s
$ Get-ChildItem docs\ADR | Select-Object Name, Length | Format-Table -AutoSize
Name       Length
----       ------
ADR-001.md      0
ADR-002.md      0
ADR-003.md      0
ADR-004.md      0
ADR-005.md      0
ADR-006.md   7329
ADR-007.md      0
ADR-008.md      0
…
Click to expand
+ Thought: 4.3s
CONFIRMED : seuls ADR-006, ADR-012, ADR-013 ont du contenu ; les 17 autres sont des fichiers vides. Je lis intégralement les deux ADR critiques pour SPR-014.
