<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\NexusSchool\app\Domains\Academic\Infrastructure\Models\SchoolClass.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domains\Academic\Infrastructure\Models\SchoolClass
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-49468322ca59fb438961b2fc2b7a098d68d106e1f01dc4f14156090c75c3edb3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'filename' => 'C:/xampp/htdocs/NexusSchool/app/Domains/Academic/Infrastructure/Models/SchoolClass.php',
      ),
    ),
    'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
    'name' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
    'shortName' => 'SchoolClass',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * A concrete class (grade group) inside a program, e.g. "6ème" in the
 * "Collège" program. May belong to an optional stream and own several sections.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 19,
    'endLine' => 69,
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
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'school_id\', \'academic_program_id\', \'education_level_id\', \'stream_id\', \'name\', \'code\', \'capacity\', \'description\', \'status\']',
          'attributes' => 
          array (
            'startLine' => 25,
            'endLine' => 35,
            'startTokenPos' => 83,
            'startFilePos' => 701,
            'endTokenPos' => 112,
            'endFilePos' => 903,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 25,
        'endLine' => 35,
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
        'startLine' => 37,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'aliasName' => NULL,
      ),
      'program' => 
      array (
        'name' => 'program',
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
        'startLine' => 45,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'aliasName' => NULL,
      ),
      'educationLevel' => 
      array (
        'name' => 'educationLevel',
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
        'startLine' => 50,
        'endLine' => 53,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'aliasName' => NULL,
      ),
      'stream' => 
      array (
        'name' => 'stream',
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
        'startLine' => 55,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'aliasName' => NULL,
      ),
      'sections' => 
      array (
        'name' => 'sections',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 60,
        'endLine' => 63,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'aliasName' => NULL,
      ),
      'coefficients' => 
      array (
        'name' => 'coefficients',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 65,
        'endLine' => 68,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
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