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








vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo php artisan config:clear

   INFO  Configuration cache cleared successfully.  

vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ php migrate:status
Could not open input file: migrate:status
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ php artisan migrate:status

  Migration name ....................................................................... Batch / Status  
  0001_01_01_000000_create_users_table ........................................................ [1] Ran  
  2019_09_15_000010_create_tenants_table ...................................................... [1] Ran  
  2019_09_15_000020_create_domains_table ...................................................... [1] Ran  
  2026_07_13_000001_create_cache_table ........................................................ [1] Ran  
  2026_07_14_000001_create_schools_table ...................................................... [1] Ran  
  2026_07_14_000002_create_permission_tables .................................................. [1] Ran  
  2026_07_14_000003_create_academic_years_table ............................................... [1] Ran  
  2026_07_14_000004_create_academic_terms_table ............................................... [1] Ran  
  2026_07_14_000005_create_departments_table .................................................. [1] Ran  
  2026_07_14_000006_create_programs_table ..................................................... [1] Ran  
  2026_07_14_000007_create_grade_levels_table ................................................. [1] Ran  
  2026_07_14_000008_create_sections_table ..................................................... [1] Ran  
  2026_07_14_000009_create_academic_classes_table ............................................. [1] Ran  
  2026_07_14_000010_create_classrooms_table ................................................... [1] Ran  
  2026_07_14_000011_add_classroom_id_to_academic_classes_table ................................ [1] Ran  
  2026_07_14_000012_create_subject_categories_table ........................................... [1] Ran  
  2026_07_14_000013_create_subjects_table ..................................................... [1] Ran  
  2026_07_14_000014_create_subject_coefficients_table ......................................... [1] Ran  
  2026_07_15_000015_create_guardians_table .................................................... [1] Ran  
  2026_07_15_000016_create_students_table ..................................................... [1] Ran  
  2026_07_15_000017_create_student_enrollments_table .......................................... [1] Ran  
  2026_07_15_000018_create_timetable_entries_table ............................................ [1] Ran  
  2026_07_16_000021_create_authors_table ...................................................... [1] Ran  
  2026_07_16_000022_create_publishers_table ................................................... [1] Ran  
  2026_07_16_000023_create_book_categories_table .............................................. [1] Ran  
  2026_07_16_000024_create_books_table ........................................................ [1] Ran  
  2026_07_16_000025_create_book_copies_table .................................................. [1] Ran  
  2026_07_16_000026_create_book_loans_table ................................................... [1] Ran  
  2026_07_16_000027_create_reservations_table ................................................. [1] Ran  
  2026_07_16_000028_create_fines_table ........................................................ [1] Ran  
  2026_07_16_000029_create_book_author_table .................................................. [1] Ran  
  2026_07_16_000030_create_vehicles_table ..................................................... [1] Ran  
  2026_07_16_000031_create_drivers_table ...................................................... [1] Ran  
  2026_07_16_000032_create_routes_table ....................................................... [1] Ran  
  2026_07_16_000033_create_route_stops_table .................................................. [1] Ran  
  2026_07_16_000034_create_trips_table ........................................................ [1] Ran  
  2026_07_16_000035_create_student_transport_assignments_table ................................ [1] Ran  
  2026_07_16_000036_create_vehicle_maintenances_table ......................................... [1] Ran  
  2026_07_16_000037_create_fuel_logs_table .................................................... [1] Ran  
  2026_07_16_000038_create_notification_templates_table ....................................... [1] Ran  
  2026_07_16_000040_create_notifications_table ................................................ [1] Ran  
  2026_07_16_000041_create_announcements_table ................................................ [1] Ran  
  2026_07_16_000041_create_notification_deliveries_table ...................................... [1] Ran  
  2026_07_16_000042_create_messages_table ..................................................... [1] Ran  
  2026_07_16_000043_create_message_recipients_table ........................................... [1] Ran  
  2026_07_16_000044_create_communication_channels_table ....................................... [1] Ran  
  2026_07_16_000045_create_dashboards_table ................................................... [1] Ran  
  2026_07_16_000046_create_dashboard_widgets_table ............................................ [1] Ran  
  2026_07_16_000047_create_reports_table ...................................................... [1] Ran  
  2026_07_16_000048_create_kpis_table ......................................................... [1] Ran  
  2026_07_16_000049_create_analytics_snapshots_table .......................................... [1] Ran  
  2026_07_16_000050_create_personal_access_tokens_table ....................................... [1] Ran  
  2026_07_16_000051_create_api_clients_table .................................................. [1] Ran  
  2026_07_16_000052_create_api_tokens_table ................................................... [1] Ran  
  2026_07_16_000053_create_webhooks_table ..................................................... [1] Ran  
  2026_07_16_000054_create_webhook_deliveries_table ........................................... [1] Ran  
  2026_07_16_000055_create_api_audit_logs_table ............................................... [1] Ran  
  2026_07_17_000056_create_ai_providers_table ................................................. [1] Ran  
  2026_07_17_000057_create_ai_conversations_table ............................................. [1] Ran  
  2026_07_17_000058_create_ai_messages_table .................................................. [1] Ran  
  2026_07_17_000059_create_ai_knowledge_sources_table ......................................... [1] Ran  
  2026_07_17_000060_create_ai_prompt_templates_table .......................................... [1] Ran  
  2026_07_17_000061_create_ai_usage_logs_table ................................................ [1] Ran  
  2026_07_17_000062_create_workflows_table .................................................... [1] Ran  
  2026_07_17_000063_create_workflow_versions_table ............................................ [1] Ran  
  2026_07_17_000064_create_workflow_triggers_table ............................................ [1] Ran  
  2026_07_17_000065_create_workflow_executions_table .......................................... [1] Ran  
  2026_07_17_000066_create_workflow_tasks_table ............................................... [1] Ran  
  2026_07_17_000067_create_workflow_schedules_table ........................................... [1] Ran  
  2026_07_17_000068_create_automation_rules_table ............................................. [1] Ran  
  2026_07_17_000069_create_system_settings_table .............................................. [1] Ran  
  2026_07_17_000070_create_audit_logs_table ................................................... [1] Ran  
  2026_07_17_000071_create_activity_logs_table ................................................ [1] Ran  
  2026_07_17_000072_create_scheduled_tasks_table .............................................. [1] Ran  
  2026_07_17_000073_create_backups_table ...................................................... [1] Ran  
  2026_07_17_000074_create_system_health_table ................................................ [1] Ran  
  2026_07_17_000075_create_maintenance_windows_table .......................................... [1] Ran  
  2026_07_17_000076_create_notification_channels_table ........................................ [1] Ran  
  2026_07_17_000077_add_school_id_to_grade_levels_table ....................................... [1] Ran  
  2026_07_17_000078_add_school_id_to_sections_table ........................................... [1] Ran  
  2026_07_17_000079_add_school_id_to_academic_classes_table ................................... [1] Ran  
  2026_07_17_000080_fix_users_school_id_fk .................................................... [1] Ran  
  2026_07_17_000081_fix_subject_categories_display_order ...................................... [1] Ran  
  2026_07_17_000082_create_attendance_records_table ........................................... [1] Ran  
  2026_07_18_000001_create_plugins_table ...................................................... [1] Ran  
  2026_07_18_000002_create_plugin_settings_table .............................................. [1] Ran  
  2026_07_24_000001_create_plans_table ........................................................ [1] Ran  
  2026_07_24_000002_create_subscriptions_table ................................................ [1] Ran  
  2026_07_24_000003_create_payments_table ..................................................... [1] Ran  
  2026_07_24_000004_create_document_templates_table ........................................... [1] Ran  
  2026_07_24_000005_create_generated_documents_table .......................................... [1] Ran  
  2026_07_24_000006_create_incidents_table .................................................... [1] Ran  
  2026_07_24_000007_create_incident_student_table ............................................. [1] Ran  
  2026_07_24_000010_add_indexes_for_dashboard_widgets ......................................... [1] Ran  
  2026_07_24_000011_create_campuses_table ..................................................... [1] Ran  
  2026_07_24_185135_add_deleted_at_to_attendance_records_table ................................ [1] Ran  
  2026_08_01_000001_create_academic_calendar_years_table ...................................... [1] Ran  
  2026_08_01_000002_create_education_levels_table ............................................. [1] Ran  
  2026_08_01_000003_create_academic_programs_table ............................................ [1] Ran  
  2026_08_01_000004_create_streams_table ...................................................... [1] Ran  
  2026_08_01_000005_create_school_classes_table ............................................... [1] Ran  
  2026_08_01_000006_create_class_sections_table ............................................... [1] Ran  
  2026_08_01_000007_create_subject_groups_table ............................................... [1] Ran  
  2026_08_01_000008_create_academic_subjects_table ............................................ [1] Ran  
  2026_08_01_000009_create_academic_calendar_terms_table ...................................... [1] Ran  
  2026_08_01_000010_create_academic_calendar_events_table ..................................... [1] Ran  
  2026_08_01_000011_create_coefficients_table ................................................. [1] Ran  
  2026_08_01_000012_add_student_domain_columns_to_students_table .............................. [1] Ran  
  2026_08_01_000013_create_student_guardians_table ............................................ [1] Ran  
  2026_08_01_000014_create_enrollments_table .................................................. [1] Ran  
  2026_08_01_000015_create_enrollment_promotions_table ........................................ [1] Ran  
  2026_08_01_000016_create_enrollment_transfers_table ......................................... [1] Ran  
  2026_08_01_000017_create_enrollment_graduations_table ....................................... [1] Ran  
  2026_08_01_000018_create_enrollment_history_table ........................................... [1] Ran  
  2026_08_03_000001_create_relationship_types_table ........................................... [1] Ran  
  2026_08_03_000002_upgrade_student_guardians_table_for_relationships ......................... [1] Ran  
  2026_08_03_000003_create_student_guardian_relationship_history_table ........................ [1] Ran  
  2026_08_04_000001_create_staffs_table ....................................................... [1] Ran  
  2026_08_04_000002_create_positions_table .................................................... [1] Ran  
  2026_08_04_000003_create_employments_table .................................................. [1] Ran  
  2026_08_04_000004_create_employment_history_table ........................................... [1] Ran  
  2026_08_04_000005_create_assignments_table .................................................. [1] Ran  
  2026_08_04_000006_create_qualifications_table ............................................... [1] Ran  
  2026_08_04_000007_create_certifications_table ............................................... [1] Ran  
  2026_08_04_000008_create_staff_emergency_contacts_table ..................................... [1] Ran  
  2026_08_04_000009_create_teacher_profiles_table ............................................. [1] Ran  
  2026_08_04_000010_create_timetables_table ................................................... [1] Ran  
  2026_08_04_000011_create_timetable_versions_table ........................................... [1] Ran  
  2026_08_04_000012_create_teaching_periods_table ............................................. [1] Ran  
  2026_08_04_000013_create_time_slots_table ................................................... [1] Ran  
  2026_08_04_000014_create_schedule_entries_table ............................................. [1] Ran  
  2026_08_04_000015_create_room_assignments_table ............................................. [1] Ran  
  2026_08_04_000016_create_recurring_patterns_table ........................................... [1] Ran  
  2026_08_04_000017_create_availability_constraints_table ..................................... [1] Ran  
  2026_08_04_000018_create_timetable_conflicts_table .......................................... [1] Ran  
  2026_08_04_000101_create_attendance_sessions_table .......................................... [1] Ran  
  2026_08_04_000102_create_attendance_session_records_table ................................... [1] Ran  
  2026_08_04_000103_create_attendance_corrections_table ....................................... [1] Ran  
  2026_08_04_000104_create_attendance_alerts_table ............................................ [1] Ran  
  2026_08_04_000105_create_attendance_summaries_table ......................................... [1] Ran  
  2026_08_04_000106_create_late_arrivals_table ................................................ [1] Ran  
  2026_08_04_000107_create_early_departures_table ............................................. [1] Ran  
  2026_08_05_000001_create_assessment_types_table ............................................. [1] Ran  
  2026_08_05_000002_create_assessment_templates_table ......................................... [1] Ran  
  2026_08_05_000003_create_assessment_sessions_table .......................................... [1] Ran  
  2026_08_05_000004_create_assessments_table .................................................. [1] Ran  
  2026_08_05_000005_create_assessment_components_table ........................................ [1] Ran  
  2026_08_05_000006_create_assessment_scores_table ............................................ [1] Ran  
  2026_08_05_000007_create_assessment_remarks_table ........................................... [1] Ran  
  2026_08_05_000008_create_assessment_attachments_table ....................................... [1] Ran  
  2026_08_05_000009_create_assessment_weights_table ........................................... [1] Ran  
  2026_08_05_000010_create_assessment_statistics_table ........................................ [1] Ran  
  2026_08_06_000001_create_gradebooks_table ................................................... [1] Ran  
  2026_08_06_000001_create_publication_policies_table ......................................... [1] Ran  
  2026_08_06_000002_create_grade_entries_table ................................................ [1] Ran  
  2026_08_06_000002_create_publication_channels_table ......................................... [1] Ran  
  2026_08_06_000003_create_academic_document_templates_table .................................. [1] Ran  
  2026_08_06_000004_create_academic_documents_table ........................................... [1] Ran  
  2026_08_06_000005_create_academic_document_versions_table ................................... [1] Ran  
  2026_08_06_000006_create_document_approvals_table ........................................... [1] Ran  
  2026_08_06_000007_create_document_signatures_table .......................................... [1] Ran  
  2026_08_06_000008_create_document_recipients_table .......................................... [1] Ran  
  2026_08_06_000009_create_document_audits_table .............................................. [1] Ran  
  2026_08_06_000010_create_generated_files_table .............................................. [1] Ran  
  2026_08_06_000011_create_publication_records_table .......................................... [1] Ran  
  2026_08_06_000012_create_publication_delivery_attempts_table ................................ [1] Ran  
  2026_08_06_000013_create_report_cards_table ................................................. [1] Ran  
  2026_08_06_000014_create_report_card_entries_table .......................................... [1] Ran  
  2026_08_06_000015_create_council_sessions_table ............................................. [1] Ran  
  2026_08_06_000016_create_council_decisions_table ............................................ [1] Ran  
  2026_08_06_000017_create_council_deliberations_table ........................................ [1] Ran  
  2026_08_10_000000_create_record_lifecycles_table ............................................ [1] Ran  
  2026_08_10_000001_create_academic_policies_table ............................................ [1] Ran  
  2026_09_06_000001_create_completion_requests_table .......................................... [1] Ran  
  2026_09_06_000002_create_completion_request_entries_table ................................... [1] Ran  
  2026_09_07_000001_create_verification_identities_table ...................................... [1] Ran  
  2026_09_07_000002_add_lifecycle_columns_to_verification_identities_table .................... [1] Ran  

vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo chown -R www-data:www-data storage bootstrap/cache
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo chmod -R u+rwX,g+rwX storage bootstrap/cache
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo systemctl reload apache2
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ 






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
