<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\NexusSchool\app\Domains\Academic\Infrastructure\Models\AcademicCalendarEvent.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domains\Academic\Infrastructure\Models\AcademicCalendarEvent
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-082e6c15e29eebf2609d2e71dafcc97e64fe921433d52538fa7aca047489af3f',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarEvent',
        'filename' => 'C:/xampp/htdocs/NexusSchool/app/Domains/Academic/Infrastructure/Models/AcademicCalendarEvent.php',
      ),
    ),
    'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
    'name' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarEvent',
    'shortName' => 'AcademicCalendarEvent',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 14,
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
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarEvent',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarEvent',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'school_id\', \'calendar_year_id\', \'calendar_term_id\', \'title\', \'type\', \'starts_on\', \'ends_on\', \'is_all_day\', \'description\']',
          'attributes' => 
          array (
            'startLine' => 20,
            'endLine' => 30,
            'startTokenPos' => 76,
            'startFilePos' => 502,
            'endTokenPos' => 105,
            'endFilePos' => 703,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 20,
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
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Domains\\Academic\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarEvent',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarEvent',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarEvent',
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
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarEvent',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarEvent',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarEvent',
        'aliasName' => NULL,
      ),
      'calendarTerm' => 
      array (
        'name' => 'calendarTerm',
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
        'declaringClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarEvent',
        'implementingClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarEvent',
        'currentClassName' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarEvent',
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