<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\NexusSchool\app\Domains\Student\Application\Services\StudentNumberGenerator.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domains\Student\Application\Services\StudentNumberGenerator
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-3425bf041253013dbac634cd9d276b73c1c1ab536d856d07b0e6ca2c9a86c209',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
        'filename' => 'C:/xampp/htdocs/NexusSchool/app/Domains/Student/Application/Services/StudentNumberGenerator.php',
      ),
    ),
    'namespace' => 'App\\Domains\\Student\\Application\\Services',
    'name' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
    'shortName' => 'StudentNumberGenerator',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Generates a sequential, school-scoped student number in the format
 * "STU-{SCHOOL_PREFIX}-{SEQUENCE}". Because students are soft-deleted only,
 * the count-based sequence never re-uses a number within a school.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 34,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'PREFIX' => 
      array (
        'declaringClassName' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
        'implementingClassName' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
        'name' => 'PREFIX',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'STU\'',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 16,
            'startTokenPos' => 38,
            'startFilePos' => 428,
            'endTokenPos' => 38,
            'endFilePos' => 432,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 32,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'generate' => 
      array (
        'name' => 'generate',
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
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 30,
            'endColumn' => 45,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 18,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Application\\Services',
        'declaringClassName' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
        'implementingClassName' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
        'currentClassName' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
        'aliasName' => NULL,
      ),
      'format' => 
      array (
        'name' => 'format',
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
            'startLine' => 28,
            'endLine' => 28,
            'startColumn' => 28,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'sequence' => 
          array (
            'name' => 'sequence',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 28,
            'endLine' => 28,
            'startColumn' => 46,
            'endColumn' => 58,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 28,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Application\\Services',
        'declaringClassName' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
        'implementingClassName' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
        'currentClassName' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
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