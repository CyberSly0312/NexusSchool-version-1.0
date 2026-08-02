<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\NexusSchool\app\Domains\Student\Infrastructure\Models\StudentGuardian.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domains\Student\Infrastructure\Models\StudentGuardian
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-73fa00c8911503a78bea03c5b7030cc00cb13adadd30f4bc5dccad81ad653ae6',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
        'filename' => 'C:/xampp/htdocs/NexusSchool/app/Domains/Student/Infrastructure/Models/StudentGuardian.php',
      ),
    ),
    'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
    'name' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
    'shortName' => 'StudentGuardian',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Pivot between a student and an embedded guardian reference. Kept future-ready:
 * a `guardian_id` may later link to a first-class Guardian entity.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 17,
    'endLine' => 46,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      1 => 'App\\Traits\\HasSchoolId',
      2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'uuid\', \'school_id\', \'student_id\', \'guardian_id\', \'name\', \'phone\', \'email\', \'relationship\', \'is_primary\']',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 33,
            'startTokenPos' => 73,
            'startFilePos' => 596,
            'endTokenPos' => 102,
            'endFilePos' => 780,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      'casts' => 
      array (
        'name' => 'casts',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 35,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
        'aliasName' => NULL,
      ),
      'student' => 
      array (
        'name' => 'student',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 42,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
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