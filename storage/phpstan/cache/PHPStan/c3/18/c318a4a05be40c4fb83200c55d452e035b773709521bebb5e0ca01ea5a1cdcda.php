<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\NexusSchool\app\Domains\Student\Presentation\Filament\Resources\StudentResource\Pages\CreateStudent.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domains\Student\Presentation\Filament\Resources\StudentResource\Pages\CreateStudent
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-44872b2abf1c414f48ffed9d8200d3137f945db9c311dcc9948993f3d963b69c',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
        'filename' => 'C:/xampp/htdocs/NexusSchool/app/Domains/Student/Presentation/Filament/Resources/StudentResource/Pages/CreateStudent.php',
      ),
    ),
    'namespace' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages',
    'name' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
    'shortName' => 'CreateStudent',
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
    'endLine' => 48,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Filament\\Resources\\Pages\\CreateRecord',
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
      'resource' => 
      array (
        'declaringClassName' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
        'implementingClassName' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
        'name' => 'resource',
        'modifiers' => 18,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '\\App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource::class',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 16,
            'startTokenPos' => 65,
            'startFilePos' => 543,
            'endTokenPos' => 67,
            'endFilePos' => 564,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 63,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'title' => 
      array (
        'declaringClassName' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
        'implementingClassName' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
        'name' => 'title',
        'modifiers' => 18,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'default' => 
        array (
          'code' => '\'Créer un élève\'',
          'attributes' => 
          array (
            'startLine' => 18,
            'endLine' => 18,
            'startTokenPos' => 81,
            'startFilePos' => 606,
            'endTokenPos' => 81,
            'endFilePos' => 624,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 58,
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
      'handleRecordCreation' => 
      array (
        'name' => 'handleRecordCreation',
        'parameters' => 
        array (
          'data' => 
          array (
            'name' => 'data',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
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
            'startColumn' => 45,
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
            'name' => 'Illuminate\\Database\\Eloquent\\Model',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 20,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages',
        'declaringClassName' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
        'implementingClassName' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
        'currentClassName' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
        'aliasName' => NULL,
      ),
      'getRedirectUrl' => 
      array (
        'name' => 'getRedirectUrl',
        'parameters' => 
        array (
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
        'startLine' => 32,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages',
        'declaringClassName' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
        'implementingClassName' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
        'currentClassName' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
        'aliasName' => NULL,
      ),
      'buildGuardians' => 
      array (
        'name' => 'buildGuardians',
        'parameters' => 
        array (
          'rawGuardians' => 
          array (
            'name' => 'rawGuardians',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 41,
            'endLine' => 41,
            'startColumn' => 37,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  list<array<string, mixed>>  $rawGuardians
 * @return list<GuardianReference>
 */',
        'startLine' => 41,
        'endLine' => 47,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages',
        'declaringClassName' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
        'implementingClassName' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
        'currentClassName' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
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