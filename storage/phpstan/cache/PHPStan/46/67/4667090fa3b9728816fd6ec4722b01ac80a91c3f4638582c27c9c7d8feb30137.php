<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\NexusSchool\app\Domains\Academic\Domain\Events\StreamCreated.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domains\Academic\Domain\Events\StreamCreated
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-be64b689388ad2d0dfb103fca303bd87cbc1ca20e275c2f93c0f330d586e61e1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domains\\Academic\\Domain\\Events\\StreamCreated',
        'filename' => 'C:/xampp/htdocs/NexusSchool/app/Domains/Academic/Domain/Events/StreamCreated.php',
      ),
    ),
    'namespace' => 'App\\Domains\\Academic\\Domain\\Events',
    'name' => 'App\\Domains\\Academic\\Domain\\Events\\StreamCreated',
    'shortName' => 'StreamCreated',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 12,
    'endLine' => 22,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
      1 => 'Illuminate\\Queue\\SerializesModels',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'stream' => 
      array (
        'declaringClassName' => 'App\\Domains\\Academic\\Domain\\Events\\StreamCreated',
        'implementingClassName' => 'App\\Domains\\Academic\\Domain\\Events\\StreamCreated',
        'name' => 'stream',
        'modifiers' => 2177,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Domains\\Academic\\Infrastructure\\Models\\Stream',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 9,
        'endColumn' => 38,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'actor' => 
      array (
        'declaringClassName' => 'App\\Domains\\Academic\\Domain\\Events\\StreamCreated',
        'implementingClassName' => 'App\\Domains\\Academic\\Domain\\Events\\StreamCreated',
        'name' => 'actor',
        'modifiers' => 2177,
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
                  'name' => 'App\\Models\\User',
                  'isIdentifier' => false,
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
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 19,
            'startTokenPos' => 78,
            'startFilePos' => 426,
            'endTokenPos' => 78,
            'endFilePos' => 429,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 9,
        'endColumn' => 43,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'stream' => 
          array (
            'name' => 'stream',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Domains\\Academic\\Infrastructure\\Models\\Stream',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 9,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'actor' => 
          array (
            'name' => 'actor',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 19,
                'endLine' => 19,
                'startTokenPos' => 78,
                'startFilePos' => 426,
                'endTokenPos' => 78,
                'endFilePos' => 429,
              ),
            ),
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
                      'name' => 'App\\Models\\User',
                      'isIdentifier' => false,
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 19,
            'endLine' => 19,
            'startColumn' => 9,
            'endColumn' => 43,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 17,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Academic\\Domain\\Events',
        'declaringClassName' => 'App\\Domains\\Academic\\Domain\\Events\\StreamCreated',
        'implementingClassName' => 'App\\Domains\\Academic\\Domain\\Events\\StreamCreated',
        'currentClassName' => 'App\\Domains\\Academic\\Domain\\Events\\StreamCreated',
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