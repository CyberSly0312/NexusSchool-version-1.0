<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\NexusSchool\app\Domains\Academic\Infrastructure\Models\AcademicProgram.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domains\Academic\Infrastructure\Models\AcademicProgram
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-7b73899bf9b03e0d9da34ebc0e2caa9b2be879c7dad5f12ec41f9ac60f78c26a',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicProgram',
        'filename' => 'C:/xampp/htdocs/NexusSchool/app/Domains/Academic/Infrastructure/Models/AcademicProgram.php',
      ),
    ),
    'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
    'name' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicProgram',
    'shortName' => 'AcademicProgram',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 15,
    'endLine' => 51,
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
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicProgram',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicProgram',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'school_id\', \'education_level_id\', \'name\', \'code\', \'description\', \'status\']',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 28,
            'startTokenPos' => 81,
            'startFilePos' => 543,
            'endTokenPos' => 101,
            'endFilePos' => 673,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 28,
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
        'startLine' => 30,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicProgram',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicProgram',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicProgram',
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
        'startLine' => 37,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicProgram',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicProgram',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicProgram',
        'aliasName' => NULL,
      ),
      'streams' => 
      array (
        'name' => 'streams',
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
        'startLine' => 42,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicProgram',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicProgram',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicProgram',
        'aliasName' => NULL,
      ),
      'classes' => 
      array (
        'name' => 'classes',
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
        'startLine' => 47,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicProgram',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicProgram',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicProgram',
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