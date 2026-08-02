<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\NexusSchool\app\Domains\Student\Domain\Exceptions\AcademicAssignmentException.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domains\Student\Domain\Exceptions\AcademicAssignmentException
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-5fae31856bcc9144eba40aad186fc9eb044471bdc3f4a66bd7ff3fb5b18048e6',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
        'filename' => 'C:/xampp/htdocs/NexusSchool/app/Domains/Student/Domain/Exceptions/AcademicAssignmentException.php',
      ),
    ),
    'namespace' => 'App\\Domains\\Student\\Domain\\Exceptions',
    'name' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
    'shortName' => 'AcademicAssignmentException',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Raised when the requested academic assignment (academic year / class) is not
 * available for the school the student belongs to, or is no longer active.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 29,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'RuntimeException',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'missingYear' => 
      array (
        'name' => 'missingYear',
        'parameters' => 
        array (
          'schoolId' => 
          array (
            'name' => 'schoolId',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 15,
            'endLine' => 15,
            'startColumn' => 40,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 15,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Domains\\Student\\Domain\\Exceptions',
        'declaringClassName' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
        'implementingClassName' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
        'currentClassName' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
        'aliasName' => NULL,
      ),
      'inactiveYear' => 
      array (
        'name' => 'inactiveYear',
        'parameters' => 
        array (
          'yearName' => 
          array (
            'name' => 'yearName',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 20,
            'endLine' => 20,
            'startColumn' => 41,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 20,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Domains\\Student\\Domain\\Exceptions',
        'declaringClassName' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
        'implementingClassName' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
        'currentClassName' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
        'aliasName' => NULL,
      ),
      'classMismatch' => 
      array (
        'name' => 'classMismatch',
        'parameters' => 
        array (
          'classId' => 
          array (
            'name' => 'classId',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 25,
            'endLine' => 25,
            'startColumn' => 42,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 25,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Domains\\Student\\Domain\\Exceptions',
        'declaringClassName' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
        'implementingClassName' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
        'currentClassName' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));