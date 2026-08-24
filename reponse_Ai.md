Git baseline CONFIRMED (clean, ac8acc2, main seule branche). Je lance les recherches de conventions (tenancy, soft deletes, provenance).
$ Write-Output "---RecordLifecycle---"; (git grep -l -i "RecordLifecycle" | Measure-Object).Count; Write-Output "---provenance---"; git grep -il "provenance" -- app database | Select-Object -First 10; Write-Output "---published_at cols---"; Get-ChildItem database\migrations -Filter *.php | Select-String -Pattern "archived_at|published_at" -List | Select-Object -First 15 -ExpandProperty Path; Write-Output "---TRAITS---"; git grep -l "SchoolOwned\|HasSchoolId\|ScopedByTenant\|BelongsToSchool" -- app | Select-Object -First 15
---RecordLifecycle---
0
---provenance---
---published_at cols---
C:\xampp\htdocs\NexusSchool-version-1.0\database\migrations\2026_07_16_000041_create_announcements_table.php
C:\xampp\htdocs\NexusSchool-version-1.0\database\migrations\2026_07_17_000063_create_workflow_versions_table.php
C:\xampp\htdocs\NexusSchool-version-1.0\database\migrations\2026_08_01_000012_add_student_domain_columns_to_students_table.php
C:\xampp\htdocs\NexusSchool-version-1.0\database\migrations\2026_08_04_000010_create_timetables_table.php
C:\xampp\htdocs\NexusSchool-version-1.0\database\migrations\2026_08_04_000101_create_attendance_sessions_table.php
C:\xampp\htdocs\NexusSchool-version-1.0\database\migrations\2026_08_05_000004_create_assessments_table.php
…
Click to expand
$ Write-Output "---SoftDeletes count---"; (git grep -l "SoftDeletes" -- "app/Domains/*/Infrastructure/Models").Count; Write-Output "---BASE MODEL?---"; git grep -n "abstract class.*Model" -- app/Domains/Shared app/Models | Select-Object -First 10; Write-Output "---Shared kernel structure---"; Get-ChildItem app\Domains\Shared -Recurse -Directory | Select-Object FullName
---SoftDeletes count---
0
---BASE MODEL?---
---Shared kernel structure---

FullName                                                         
--------                                                         
C:\xampp\htdocs\NexusSchool-version-1.0\app\Domains\Shared\Enums 
C:\xampp\htdocs\NexusSchool-version-1.0\app\Domains\Shared\Traits
+ Thought: 5.5s
$ Write-Output "---SoftDeletes app-wide---"; git grep -l "SoftDeletes" -- app | Measure-Object | Select-Object -ExpandProperty Count; Write-Output "---council_sessions migration---"; Get-Content database\migrations\2026_08_06_000015_create_council_sessions_table.php
---SoftDeletes app-wide---
139
---council_sessions migration---
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
…
Click to expand
+ Thought: 5.2s
Conventions de migration identifiées (school_id string(36)→tenants, ulid, softDeletes). Je lis le Shared kernel et la gouvernance.
$ Get-ChildItem docs\docs -Directory | Select-Object Name; Write-Output "---07---"; if (Test-Path docs\docs\07_Engineering_Standards) { Get-ChildItem docs\docs\07_Engineering_Standards -Recurse -File | Select-Object Name }; Write-Output "---ADR---"; Get-ChildItem docs\docs\06_ADR -File -ErrorAction SilentlyContinue | Select-Object Name; Get-ChildItem docs\ADR -File -ErrorAction SilentlyContinue | Select-Object Name
Name                 
----                 
00_Project_Management
01_Development_Guides
02_Domain            
02_Playbooks         
03_Checklists        
04_Templates         
99_Project_Journal   
---07---
…
Click to expand
$ Get-ChildItem docs\docs\06_ADR -File | Select-Object Name; Write-Output "---01-DEV-GUIDES---"; Get-ChildItem docs\docs\01_Development_Guides -Recurse -File | Select-Object Name; Write-Output "---02-PLAYBOOKS---"; Get-ChildItem docs\docs\02_Playbooks -Recurse -File | Select-Object Name | Select-Object -First 20
Get-ChildItem : Cannot find path 'C:\xampp\htdocs\NexusSchool-version-1.0\docs\docs\06_ADR' because it does not exist.
At line:1 char:1
+ Get-ChildItem docs\docs\06_ADR -File | Select-Object Name; Write-Outp ...
+ ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    + CategoryInfo          : ObjectNotFound: (C:\xampp\htdocs...ocs\docs\06_ADR:String) [Get-ChildItem], ItemNotFound 
   Exception
    + FullyQualifiedErrorId : PathNotFound,Microsoft.PowerShell.Commands.GetChildItemCommand
 
---01-DEV-GUIDES---

…
Click to expand
+ Thought: 4.9s
$ Get-ChildItem docs\ADR -File | Select-String -Pattern "^# " -List | ForEach-Object { "$($_.Filename): $($_.Line)" }
ADR-006.md: # ADR-006
ADR-012.md: # ADR-012
ADR-013.md: # ADR-013
→Read docs\docs\02_Playbooks\Create_New_Module.md 
$ foreach ($f in 'docs\ADR\ADR-012.md','docs\ADR\ADR-013.md','docs\ADR\ADR-006.md') { Write-Output "=== $f ==="; Get-Content $f -TotalCount 30 }
=== docs\ADR\ADR-012.md ===
# ADR-012
# Academic Communication Engine Architecture

**Status:** Accepted

**Date:** 2026-08-04

**Decision Makers:**

