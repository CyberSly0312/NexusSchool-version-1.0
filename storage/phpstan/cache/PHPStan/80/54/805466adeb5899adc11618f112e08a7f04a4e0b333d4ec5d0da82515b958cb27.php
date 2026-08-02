<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\NexusSchool\app\Domains\Academic\Infrastructure\Models\ClassSection.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domains\Academic\Infrastructure\Models\ClassSection
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-78450f8701784b22cd6f865520e2577ac21a5e5818ac929256c8ed330a88a41e',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domains\\Academic\\Infrastructure\\Models\\ClassSection',
        'filename' => 'C:/xampp/htdocs/NexusSchool/app/Domains/Academic/Infrastructure/Models/ClassSection.php',
      ),
    ),
    'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
    'name' => 'App\\Domains\\Academic\\Infrastructure\\Models\\ClassSection',
    'shortName' => 'ClassSection',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * A section/group inside a class (e.g. "A", "B").
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 17,
    'endLine' => 44,
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
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\ClassSection',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\ClassSection',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'school_id\', \'school_class_id\', \'name\', \'code\', \'capacity\', \'status\']',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 30,
            'startTokenPos' => 78,
            'startFilePos' => 547,
            'endTokenPos' => 98,
            'endFilePos' => 671,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 30,
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
        'startLine' => 32,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\ClassSection',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\ClassSection',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\ClassSection',
        'aliasName' => NULL,
      ),
      'schoolClass' => 
      array (
        'name' => 'schoolClass',
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
        'startLine' => 40,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\ClassSection',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\ClassSection',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\ClassSection',
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