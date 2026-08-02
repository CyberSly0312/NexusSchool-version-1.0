<?php declare(strict_types = 1);

// ftm-C:\xampp\htdocs\NexusSchool\app\Domains\Student\StudentServiceProvider.php
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v5-2.3.3',
   'data' => 
  array (
    0 => 
    array (
      '30c4fca07fbd0c0bcd6c1751e8af4439' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domains\\Student',
         'uses' => 
        array (
          'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
          'studentservice' => 'App\\Domains\\Student\\Application\\Services\\StudentService',
          'studentarchived' => 'App\\Domains\\Student\\Domain\\Events\\StudentArchived',
          'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
          'studentgraduated' => 'App\\Domains\\Student\\Domain\\Events\\StudentGraduated',
          'studentreactivated' => 'App\\Domains\\Student\\Domain\\Events\\StudentReactivated',
          'studentrestored' => 'App\\Domains\\Student\\Domain\\Events\\StudentRestored',
          'studentsuspended' => 'App\\Domains\\Student\\Domain\\Events\\StudentSuspended',
          'studenttransferred' => 'App\\Domains\\Student\\Domain\\Events\\StudentTransferred',
          'studentupdated' => 'App\\Domains\\Student\\Domain\\Events\\StudentUpdated',
          'studentwithdrawn' => 'App\\Domains\\Student\\Domain\\Events\\StudentWithdrawn',
          'studentauditlogger' => 'App\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
          'studententityobserver' => 'App\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver',
          'studentpolicy' => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
          'event' => 'Illuminate\\Support\\Facades\\Event',
          'gate' => 'Illuminate\\Support\\Facades\\Gate',
          'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
        ),
         'className' => 'App\\Domains\\Student\\StudentServiceProvider',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'f3c94a741d8a7c02d117c02a84b61429' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domains\\Student',
         'uses' => 
        array (
          'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
          'studentservice' => 'App\\Domains\\Student\\Application\\Services\\StudentService',
          'studentarchived' => 'App\\Domains\\Student\\Domain\\Events\\StudentArchived',
          'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
          'studentgraduated' => 'App\\Domains\\Student\\Domain\\Events\\StudentGraduated',
          'studentreactivated' => 'App\\Domains\\Student\\Domain\\Events\\StudentReactivated',
          'studentrestored' => 'App\\Domains\\Student\\Domain\\Events\\StudentRestored',
          'studentsuspended' => 'App\\Domains\\Student\\Domain\\Events\\StudentSuspended',
          'studenttransferred' => 'App\\Domains\\Student\\Domain\\Events\\StudentTransferred',
          'studentupdated' => 'App\\Domains\\Student\\Domain\\Events\\StudentUpdated',
          'studentwithdrawn' => 'App\\Domains\\Student\\Domain\\Events\\StudentWithdrawn',
          'studentauditlogger' => 'App\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
          'studententityobserver' => 'App\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver',
          'studentpolicy' => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
          'event' => 'Illuminate\\Support\\Facades\\Event',
          'gate' => 'Illuminate\\Support\\Facades\\Gate',
          'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
        ),
         'className' => 'App\\Domains\\Student\\StudentServiceProvider',
         'functionName' => 'register',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Domains\\Student',
           'uses' => 
          array (
            'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
            'studentservice' => 'App\\Domains\\Student\\Application\\Services\\StudentService',
            'studentarchived' => 'App\\Domains\\Student\\Domain\\Events\\StudentArchived',
            'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
            'studentgraduated' => 'App\\Domains\\Student\\Domain\\Events\\StudentGraduated',
            'studentreactivated' => 'App\\Domains\\Student\\Domain\\Events\\StudentReactivated',
            'studentrestored' => 'App\\Domains\\Student\\Domain\\Events\\StudentRestored',
            'studentsuspended' => 'App\\Domains\\Student\\Domain\\Events\\StudentSuspended',
            'studenttransferred' => 'App\\Domains\\Student\\Domain\\Events\\StudentTransferred',
            'studentupdated' => 'App\\Domains\\Student\\Domain\\Events\\StudentUpdated',
            'studentwithdrawn' => 'App\\Domains\\Student\\Domain\\Events\\StudentWithdrawn',
            'studentauditlogger' => 'App\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger',
            'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
            'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
            'studententityobserver' => 'App\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver',
            'studentpolicy' => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
            'event' => 'Illuminate\\Support\\Facades\\Event',
            'gate' => 'Illuminate\\Support\\Facades\\Gate',
            'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
          ),
           'className' => 'App\\Domains\\Student\\StudentServiceProvider',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'a196fa6b3a708ad2e8eab04f4761ea4f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domains\\Student',
         'uses' => 
        array (
          'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
          'studentservice' => 'App\\Domains\\Student\\Application\\Services\\StudentService',
          'studentarchived' => 'App\\Domains\\Student\\Domain\\Events\\StudentArchived',
          'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
          'studentgraduated' => 'App\\Domains\\Student\\Domain\\Events\\StudentGraduated',
          'studentreactivated' => 'App\\Domains\\Student\\Domain\\Events\\StudentReactivated',
          'studentrestored' => 'App\\Domains\\Student\\Domain\\Events\\StudentRestored',
          'studentsuspended' => 'App\\Domains\\Student\\Domain\\Events\\StudentSuspended',
          'studenttransferred' => 'App\\Domains\\Student\\Domain\\Events\\StudentTransferred',
          'studentupdated' => 'App\\Domains\\Student\\Domain\\Events\\StudentUpdated',
          'studentwithdrawn' => 'App\\Domains\\Student\\Domain\\Events\\StudentWithdrawn',
          'studentauditlogger' => 'App\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
          'studententityobserver' => 'App\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver',
          'studentpolicy' => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
          'event' => 'Illuminate\\Support\\Facades\\Event',
          'gate' => 'Illuminate\\Support\\Facades\\Gate',
          'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
        ),
         'className' => 'App\\Domains\\Student\\StudentServiceProvider',
         'functionName' => 'boot',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Domains\\Student',
           'uses' => 
          array (
            'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
            'studentservice' => 'App\\Domains\\Student\\Application\\Services\\StudentService',
            'studentarchived' => 'App\\Domains\\Student\\Domain\\Events\\StudentArchived',
            'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
            'studentgraduated' => 'App\\Domains\\Student\\Domain\\Events\\StudentGraduated',
            'studentreactivated' => 'App\\Domains\\Student\\Domain\\Events\\StudentReactivated',
            'studentrestored' => 'App\\Domains\\Student\\Domain\\Events\\StudentRestored',
            'studentsuspended' => 'App\\Domains\\Student\\Domain\\Events\\StudentSuspended',
            'studenttransferred' => 'App\\Domains\\Student\\Domain\\Events\\StudentTransferred',
            'studentupdated' => 'App\\Domains\\Student\\Domain\\Events\\StudentUpdated',
            'studentwithdrawn' => 'App\\Domains\\Student\\Domain\\Events\\StudentWithdrawn',
            'studentauditlogger' => 'App\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger',
            'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
            'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
            'studententityobserver' => 'App\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver',
            'studentpolicy' => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
            'event' => 'Illuminate\\Support\\Facades\\Event',
            'gate' => 'Illuminate\\Support\\Facades\\Gate',
            'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
          ),
           'className' => 'App\\Domains\\Student\\StudentServiceProvider',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '5755cffb8d953da399b478f12cd89d08' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domains\\Student',
         'uses' => 
        array (
          'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
          'studentservice' => 'App\\Domains\\Student\\Application\\Services\\StudentService',
          'studentarchived' => 'App\\Domains\\Student\\Domain\\Events\\StudentArchived',
          'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
          'studentgraduated' => 'App\\Domains\\Student\\Domain\\Events\\StudentGraduated',
          'studentreactivated' => 'App\\Domains\\Student\\Domain\\Events\\StudentReactivated',
          'studentrestored' => 'App\\Domains\\Student\\Domain\\Events\\StudentRestored',
          'studentsuspended' => 'App\\Domains\\Student\\Domain\\Events\\StudentSuspended',
          'studenttransferred' => 'App\\Domains\\Student\\Domain\\Events\\StudentTransferred',
          'studentupdated' => 'App\\Domains\\Student\\Domain\\Events\\StudentUpdated',
          'studentwithdrawn' => 'App\\Domains\\Student\\Domain\\Events\\StudentWithdrawn',
          'studentauditlogger' => 'App\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
          'studententityobserver' => 'App\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver',
          'studentpolicy' => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
          'event' => 'Illuminate\\Support\\Facades\\Event',
          'gate' => 'Illuminate\\Support\\Facades\\Gate',
          'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
        ),
         'className' => 'App\\Domains\\Student\\StudentServiceProvider',
         'functionName' => 'registerObservers',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Domains\\Student',
           'uses' => 
          array (
            'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
            'studentservice' => 'App\\Domains\\Student\\Application\\Services\\StudentService',
            'studentarchived' => 'App\\Domains\\Student\\Domain\\Events\\StudentArchived',
            'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
            'studentgraduated' => 'App\\Domains\\Student\\Domain\\Events\\StudentGraduated',
            'studentreactivated' => 'App\\Domains\\Student\\Domain\\Events\\StudentReactivated',
            'studentrestored' => 'App\\Domains\\Student\\Domain\\Events\\StudentRestored',
            'studentsuspended' => 'App\\Domains\\Student\\Domain\\Events\\StudentSuspended',
            'studenttransferred' => 'App\\Domains\\Student\\Domain\\Events\\StudentTransferred',
            'studentupdated' => 'App\\Domains\\Student\\Domain\\Events\\StudentUpdated',
            'studentwithdrawn' => 'App\\Domains\\Student\\Domain\\Events\\StudentWithdrawn',
            'studentauditlogger' => 'App\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger',
            'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
            'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
            'studententityobserver' => 'App\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver',
            'studentpolicy' => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
            'event' => 'Illuminate\\Support\\Facades\\Event',
            'gate' => 'Illuminate\\Support\\Facades\\Gate',
            'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
          ),
           'className' => 'App\\Domains\\Student\\StudentServiceProvider',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'd0e22b33048c52f48990d258171833ba' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domains\\Student',
         'uses' => 
        array (
          'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
          'studentservice' => 'App\\Domains\\Student\\Application\\Services\\StudentService',
          'studentarchived' => 'App\\Domains\\Student\\Domain\\Events\\StudentArchived',
          'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
          'studentgraduated' => 'App\\Domains\\Student\\Domain\\Events\\StudentGraduated',
          'studentreactivated' => 'App\\Domains\\Student\\Domain\\Events\\StudentReactivated',
          'studentrestored' => 'App\\Domains\\Student\\Domain\\Events\\StudentRestored',
          'studentsuspended' => 'App\\Domains\\Student\\Domain\\Events\\StudentSuspended',
          'studenttransferred' => 'App\\Domains\\Student\\Domain\\Events\\StudentTransferred',
          'studentupdated' => 'App\\Domains\\Student\\Domain\\Events\\StudentUpdated',
          'studentwithdrawn' => 'App\\Domains\\Student\\Domain\\Events\\StudentWithdrawn',
          'studentauditlogger' => 'App\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
          'studententityobserver' => 'App\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver',
          'studentpolicy' => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
          'event' => 'Illuminate\\Support\\Facades\\Event',
          'gate' => 'Illuminate\\Support\\Facades\\Gate',
          'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
        ),
         'className' => 'App\\Domains\\Student\\StudentServiceProvider',
         'functionName' => 'registerPolicies',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Domains\\Student',
           'uses' => 
          array (
            'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
            'studentservice' => 'App\\Domains\\Student\\Application\\Services\\StudentService',
            'studentarchived' => 'App\\Domains\\Student\\Domain\\Events\\StudentArchived',
            'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
            'studentgraduated' => 'App\\Domains\\Student\\Domain\\Events\\StudentGraduated',
            'studentreactivated' => 'App\\Domains\\Student\\Domain\\Events\\StudentReactivated',
            'studentrestored' => 'App\\Domains\\Student\\Domain\\Events\\StudentRestored',
            'studentsuspended' => 'App\\Domains\\Student\\Domain\\Events\\StudentSuspended',
            'studenttransferred' => 'App\\Domains\\Student\\Domain\\Events\\StudentTransferred',
            'studentupdated' => 'App\\Domains\\Student\\Domain\\Events\\StudentUpdated',
            'studentwithdrawn' => 'App\\Domains\\Student\\Domain\\Events\\StudentWithdrawn',
            'studentauditlogger' => 'App\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger',
            'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
            'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
            'studententityobserver' => 'App\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver',
            'studentpolicy' => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
            'event' => 'Illuminate\\Support\\Facades\\Event',
            'gate' => 'Illuminate\\Support\\Facades\\Gate',
            'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
          ),
           'className' => 'App\\Domains\\Student\\StudentServiceProvider',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '1501378bf7cce6a0c19c5b7437ff82ce' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domains\\Student',
         'uses' => 
        array (
          'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
          'studentservice' => 'App\\Domains\\Student\\Application\\Services\\StudentService',
          'studentarchived' => 'App\\Domains\\Student\\Domain\\Events\\StudentArchived',
          'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
          'studentgraduated' => 'App\\Domains\\Student\\Domain\\Events\\StudentGraduated',
          'studentreactivated' => 'App\\Domains\\Student\\Domain\\Events\\StudentReactivated',
          'studentrestored' => 'App\\Domains\\Student\\Domain\\Events\\StudentRestored',
          'studentsuspended' => 'App\\Domains\\Student\\Domain\\Events\\StudentSuspended',
          'studenttransferred' => 'App\\Domains\\Student\\Domain\\Events\\StudentTransferred',
          'studentupdated' => 'App\\Domains\\Student\\Domain\\Events\\StudentUpdated',
          'studentwithdrawn' => 'App\\Domains\\Student\\Domain\\Events\\StudentWithdrawn',
          'studentauditlogger' => 'App\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
          'studententityobserver' => 'App\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver',
          'studentpolicy' => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
          'event' => 'Illuminate\\Support\\Facades\\Event',
          'gate' => 'Illuminate\\Support\\Facades\\Gate',
          'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
        ),
         'className' => 'App\\Domains\\Student\\StudentServiceProvider',
         'functionName' => 'registerEvents',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Domains\\Student',
           'uses' => 
          array (
            'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
            'studentservice' => 'App\\Domains\\Student\\Application\\Services\\StudentService',
            'studentarchived' => 'App\\Domains\\Student\\Domain\\Events\\StudentArchived',
            'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
            'studentgraduated' => 'App\\Domains\\Student\\Domain\\Events\\StudentGraduated',
            'studentreactivated' => 'App\\Domains\\Student\\Domain\\Events\\StudentReactivated',
            'studentrestored' => 'App\\Domains\\Student\\Domain\\Events\\StudentRestored',
            'studentsuspended' => 'App\\Domains\\Student\\Domain\\Events\\StudentSuspended',
            'studenttransferred' => 'App\\Domains\\Student\\Domain\\Events\\StudentTransferred',
            'studentupdated' => 'App\\Domains\\Student\\Domain\\Events\\StudentUpdated',
            'studentwithdrawn' => 'App\\Domains\\Student\\Domain\\Events\\StudentWithdrawn',
            'studentauditlogger' => 'App\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger',
            'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
            'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
            'studententityobserver' => 'App\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver',
            'studentpolicy' => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
            'event' => 'Illuminate\\Support\\Facades\\Event',
            'gate' => 'Illuminate\\Support\\Facades\\Gate',
            'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
          ),
           'className' => 'App\\Domains\\Student\\StudentServiceProvider',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
    ),
    1 => 
    array (
      'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php' => '1bc1baef6d7b5077354acf96bc3b3bd55f76a9c977be414ea23dbf7da1515f2b',
    ),
  ),
));