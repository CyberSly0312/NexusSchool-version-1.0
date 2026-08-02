<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\NexusSchool\app\Domains\Academic\Infrastructure\Observers\AcademicEntityObserver.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domains\Academic\Infrastructure\Observers\AcademicEntityObserver
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-d3a8d61c7c5428c237a7e0acfdff9a0da5282c65c4c444daa1bc0488eb5e5b6e',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domains\\Academic\\Infrastructure\\Observers\\AcademicEntityObserver',
        'filename' => 'C:/xampp/htdocs/NexusSchool/app/Domains/Academic/Infrastructure/Observers/AcademicEntityObserver.php',
      ),
    ),
    'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Observers',
    'name' => 'App\\Domains\\Academic\\Infrastructure\\Observers\\AcademicEntityObserver',
    'shortName' => 'AcademicEntityObserver',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Single observer for every engine model: generates the public ULID `uuid`.
 *
 * This replaces the legacy per-model `booted()` generators and the global
 * `Model::creating` hook for the Academic domain (SPR-001 D9, scoped to this
 * domain until the app-wide ULID sprint lands).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 17,
    'endLine' => 25,
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
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'creating' => 
      array (
        'name' => 'creating',
        'parameters' => 
        array (
          'model' => 
          array (
            'name' => 'model',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Model',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 19,
            'endLine' => 19,
            'startColumn' => 30,
            'endColumn' => 41,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 19,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Observers',
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Observers\\AcademicEntityObserver',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Observers\\AcademicEntityObserver',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Observers\\AcademicEntityObserver',
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