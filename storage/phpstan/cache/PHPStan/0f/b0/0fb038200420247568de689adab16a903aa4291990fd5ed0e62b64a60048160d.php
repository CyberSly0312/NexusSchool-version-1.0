<?php declare(strict_types = 1);

// ftm-C:\xampp\htdocs\NexusSchool\app\Domains\Student\Application\Actions\CreateStudentAction.php
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v5-2.3.3',
   'data' => 
  array (
    0 => 
    array (
      'cc079669eca54bccc6fb8f68d77b306b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domains\\Student\\Application\\Actions',
         'uses' => 
        array (
          'academiccalendaryear' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarYear',
          'schoolclass' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
          'createstudentdata' => 'App\\Domains\\Student\\Application\\DTOs\\CreateStudentData',
          'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
          'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
          'academicassignmentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
          'duplicatestudentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException',
          'birthdate' => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
          'emailaddress' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
          'phonenumber' => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
          'user' => 'App\\Models\\User',
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'invalidargumentexception' => 'InvalidArgumentException',
        ),
         'className' => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
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
      'c743b80767d74c6d0ddf2ef65606cd85' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domains\\Student\\Application\\Actions',
         'uses' => 
        array (
          'academiccalendaryear' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarYear',
          'schoolclass' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
          'createstudentdata' => 'App\\Domains\\Student\\Application\\DTOs\\CreateStudentData',
          'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
          'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
          'academicassignmentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
          'duplicatestudentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException',
          'birthdate' => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
          'emailaddress' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
          'phonenumber' => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
          'user' => 'App\\Models\\User',
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'invalidargumentexception' => 'InvalidArgumentException',
        ),
         'className' => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
         'functionName' => '__construct',
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
      '193d4faec41511437ff3cd747311e6a8' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domains\\Student\\Application\\Actions',
         'uses' => 
        array (
          'academiccalendaryear' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarYear',
          'schoolclass' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
          'createstudentdata' => 'App\\Domains\\Student\\Application\\DTOs\\CreateStudentData',
          'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
          'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
          'academicassignmentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
          'duplicatestudentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException',
          'birthdate' => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
          'emailaddress' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
          'phonenumber' => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
          'user' => 'App\\Models\\User',
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'invalidargumentexception' => 'InvalidArgumentException',
        ),
         'className' => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
         'functionName' => 'execute',
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
      '39c23d2d4dc751277a60322a77e4a368' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domains\\Student\\Application\\Actions',
         'uses' => 
        array (
          'academiccalendaryear' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarYear',
          'schoolclass' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
          'createstudentdata' => 'App\\Domains\\Student\\Application\\DTOs\\CreateStudentData',
          'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
          'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
          'academicassignmentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
          'duplicatestudentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException',
          'birthdate' => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
          'emailaddress' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
          'phonenumber' => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
          'user' => 'App\\Models\\User',
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'invalidargumentexception' => 'InvalidArgumentException',
        ),
         'className' => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
         'functionName' => 'ensureStudentNumber',
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
      'b8148262c75da416832957714f172887' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domains\\Student\\Application\\Actions',
         'uses' => 
        array (
          'academiccalendaryear' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarYear',
          'schoolclass' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
          'createstudentdata' => 'App\\Domains\\Student\\Application\\DTOs\\CreateStudentData',
          'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
          'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
          'academicassignmentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
          'duplicatestudentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException',
          'birthdate' => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
          'emailaddress' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
          'phonenumber' => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
          'user' => 'App\\Models\\User',
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'invalidargumentexception' => 'InvalidArgumentException',
        ),
         'className' => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
         'functionName' => 'normalizeValueObjects',
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
      '94cc7e9f686c87d196edd9538464bcbf' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domains\\Student\\Application\\Actions',
         'uses' => 
        array (
          'academiccalendaryear' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarYear',
          'schoolclass' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
          'createstudentdata' => 'App\\Domains\\Student\\Application\\DTOs\\CreateStudentData',
          'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
          'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
          'academicassignmentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
          'duplicatestudentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException',
          'birthdate' => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
          'emailaddress' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
          'phonenumber' => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
          'user' => 'App\\Models\\User',
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'invalidargumentexception' => 'InvalidArgumentException',
        ),
         'className' => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
         'functionName' => 'assertNoDuplicate',
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
      'a2b870cd5ea28ee2d7ec1b1800a49de0' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domains\\Student\\Application\\Actions',
         'uses' => 
        array (
          'academiccalendaryear' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarYear',
          'schoolclass' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
          'createstudentdata' => 'App\\Domains\\Student\\Application\\DTOs\\CreateStudentData',
          'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
          'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
          'academicassignmentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
          'duplicatestudentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException',
          'birthdate' => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
          'emailaddress' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
          'phonenumber' => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
          'user' => 'App\\Models\\User',
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'invalidargumentexception' => 'InvalidArgumentException',
        ),
         'className' => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
         'functionName' => 'assertValidBirthDate',
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
      '049275fd97b9da5ee2f43622d208c6ee' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domains\\Student\\Application\\Actions',
         'uses' => 
        array (
          'academiccalendaryear' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarYear',
          'schoolclass' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
          'createstudentdata' => 'App\\Domains\\Student\\Application\\DTOs\\CreateStudentData',
          'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
          'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
          'academicassignmentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
          'duplicatestudentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException',
          'birthdate' => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
          'emailaddress' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
          'phonenumber' => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
          'user' => 'App\\Models\\User',
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'invalidargumentexception' => 'InvalidArgumentException',
        ),
         'className' => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
         'functionName' => 'assertValidAcademicAssignment',
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
      'e7fb44872258eeeedbb1ed18b94f4842' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domains\\Student\\Application\\Actions',
         'uses' => 
        array (
          'academiccalendaryear' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarYear',
          'schoolclass' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
          'createstudentdata' => 'App\\Domains\\Student\\Application\\DTOs\\CreateStudentData',
          'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
          'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
          'academicassignmentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
          'duplicatestudentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException',
          'birthdate' => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
          'emailaddress' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
          'phonenumber' => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
          'user' => 'App\\Models\\User',
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'invalidargumentexception' => 'InvalidArgumentException',
        ),
         'className' => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
         'functionName' => 'syncGuardians',
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
      '6e3b82895f2e237956fbdd162dc798fd' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domains\\Student\\Application\\Actions',
         'uses' => 
        array (
          'academiccalendaryear' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarYear',
          'schoolclass' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
          'createstudentdata' => 'App\\Domains\\Student\\Application\\DTOs\\CreateStudentData',
          'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
          'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
          'academicassignmentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
          'duplicatestudentexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException',
          'birthdate' => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
          'emailaddress' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
          'phonenumber' => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
          'user' => 'App\\Models\\User',
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'invalidargumentexception' => 'InvalidArgumentException',
        ),
         'className' => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
         'functionName' => 'isPrimaryGuardian',
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
    ),
    1 => 
    array (
      'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php' => '5bc12c51f21cde0c3ae6e6b7f5e45b1d7f71539f016c203fb00f48e1d2ef0eb5',
    ),
  ),
));