<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\NexusSchool\app\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-6c75b63f5ebbf872397e50ca91ec2d29989823d3c71f76cd0175d7fc7284c684',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'filename' => 'C:/xampp/htdocs/NexusSchool/app/Domains/Academic/Infrastructure/Models/AcademicCalendarTerm.php',
      ),
    ),
    'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
    'name' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
    'shortName' => 'AcademicCalendarTerm',
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
    'endLine' => 64,
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
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'school_id\', \'calendar_year_id\', \'name\', \'code\', \'starts_on\', \'ends_on\', \'status\']',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 29,
            'startTokenPos' => 81,
            'startFilePos' => 554,
            'endTokenPos' => 104,
            'endFilePos' => 699,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 29,
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
        'startLine' => 31,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'aliasName' => NULL,
      ),
      'calendarYear' => 
      array (
        'name' => 'calendarYear',
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
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'aliasName' => NULL,
      ),
      'events' => 
      array (
        'name' => 'events',
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
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'aliasName' => NULL,
      ),
      'isOpen' => 
      array (
        'name' => 'isOpen',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
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
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'aliasName' => NULL,
      ),
      'isPlanned' => 
      array (
        'name' => 'isPlanned',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
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
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'aliasName' => NULL,
      ),
      'isClosed' => 
      array (
        'name' => 'isClosed',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
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
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm',
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