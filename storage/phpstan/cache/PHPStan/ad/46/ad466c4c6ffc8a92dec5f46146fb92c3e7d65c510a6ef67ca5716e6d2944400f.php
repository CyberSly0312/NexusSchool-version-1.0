<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\NexusSchool\app\Models\VehicleMaintenance.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\VehicleMaintenance
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-893669c8a6843bb30046bf107e18313a9c16ae34016ac51170a80538814b065e',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\VehicleMaintenance',
        'filename' => 'C:/xampp/htdocs/NexusSchool/app/Models/VehicleMaintenance.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\VehicleMaintenance',
    'shortName' => 'VehicleMaintenance',
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
    'endLine' => 76,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      1 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'incrementing' => 
      array (
        'declaringClassName' => 'App\\Models\\VehicleMaintenance',
        'implementingClassName' => 'App\\Models\\VehicleMaintenance',
        'name' => 'incrementing',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 20,
            'endLine' => 20,
            'startTokenPos' => 76,
            'startFilePos' => 490,
            'endTokenPos' => 76,
            'endFilePos' => 494,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'keyType' => 
      array (
        'declaringClassName' => 'App\\Models\\VehicleMaintenance',
        'implementingClassName' => 'App\\Models\\VehicleMaintenance',
        'name' => 'keyType',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'string\'',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 85,
            'startFilePos' => 523,
            'endTokenPos' => 85,
            'endFilePos' => 530,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\VehicleMaintenance',
        'implementingClassName' => 'App\\Models\\VehicleMaintenance',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'school_id\', \'vehicle_id\', \'type\', \'description\', \'cost\', \'service_provider\', \'service_date\', \'next_service_date\', \'odometer_reading\', \'receipt_document\', \'status\', \'completed_at\', \'notes\']',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 38,
            'startTokenPos' => 94,
            'startFilePos' => 560,
            'endTokenPos' => 135,
            'endFilePos' => 860,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'casts' => 
      array (
        'declaringClassName' => 'App\\Models\\VehicleMaintenance',
        'implementingClassName' => 'App\\Models\\VehicleMaintenance',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'cost\' => \'decimal:2\', \'service_date\' => \'date\', \'next_service_date\' => \'date\', \'odometer_reading\' => \'integer\', \'completed_at\' => \'datetime\', \'type\' => \\App\\Domains\\Transport\\Enums\\MaintenanceType::class, \'status\' => \\App\\Domains\\Transport\\Enums\\MaintenanceStatus::class]',
          'attributes' => 
          array (
            'startLine' => 40,
            'endLine' => 48,
            'startTokenPos' => 144,
            'startFilePos' => 887,
            'endTokenPos' => 199,
            'endFilePos' => 1164,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 40,
        'endLine' => 48,
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
      'boot' => 
      array (
        'name' => 'boot',
        'parameters' => 
        array (
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
        'startLine' => 50,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\VehicleMaintenance',
        'implementingClassName' => 'App\\Models\\VehicleMaintenance',
        'currentClassName' => 'App\\Models\\VehicleMaintenance',
        'aliasName' => NULL,
      ),
      'school' => 
      array (
        'name' => 'school',
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
        'startLine' => 67,
        'endLine' => 70,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\VehicleMaintenance',
        'implementingClassName' => 'App\\Models\\VehicleMaintenance',
        'currentClassName' => 'App\\Models\\VehicleMaintenance',
        'aliasName' => NULL,
      ),
      'vehicle' => 
      array (
        'name' => 'vehicle',
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
        'startLine' => 72,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\VehicleMaintenance',
        'implementingClassName' => 'App\\Models\\VehicleMaintenance',
        'currentClassName' => 'App\\Models\\VehicleMaintenance',
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