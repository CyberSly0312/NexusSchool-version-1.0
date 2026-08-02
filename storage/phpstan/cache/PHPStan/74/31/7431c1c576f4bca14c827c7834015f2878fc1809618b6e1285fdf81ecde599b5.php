<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\NexusSchool\app\Domains\Student\Domain\ValueObjects\GuardianReference.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domains\Student\Domain\ValueObjects\GuardianReference
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-03f438ec7e31750c8dc8d848240c2fa7291fb2dc9e1a321936bc3bd54de92aa3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'filename' => 'C:/xampp/htdocs/NexusSchool/app/Domains/Student/Domain/ValueObjects/GuardianReference.php',
      ),
    ),
    'namespace' => 'App\\Domains\\Student\\Domain\\ValueObjects',
    'name' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
    'shortName' => 'GuardianReference',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 65568,
    'docComment' => '/**
 * A self-contained reference to a student\'s guardian. Guardians may later be
 * upgraded to first-class entities; the value object keeps the domain stable
 * in the meantime.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 106,
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
      'RELATIONSHIPS' => 
      array (
        'declaringClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'implementingClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'name' => 'RELATIONSHIPS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'parent\', \'guardian\', \'sibling\', \'other\']',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 21,
            'startTokenPos' => 40,
            'startFilePos' => 374,
            'endTokenPos' => 54,
            'endFilePos' => 454,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
      'name' => 
      array (
        'declaringClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'implementingClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'name' => 'name',
        'modifiers' => 2049,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 24,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'phone' => 
      array (
        'declaringClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'implementingClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'name' => 'phone',
        'modifiers' => 2049,
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
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 26,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'email' => 
      array (
        'declaringClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'implementingClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'name' => 'email',
        'modifiers' => 2049,
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
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 26,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'relationship' => 
      array (
        'declaringClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'implementingClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'name' => 'relationship',
        'modifiers' => 2049,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 29,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'isPrimary' => 
      array (
        'declaringClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'implementingClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'name' => 'isPrimary',
        'modifiers' => 2049,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 27,
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
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 34,
            'endLine' => 34,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'phone' => 
          array (
            'name' => 'phone',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 35,
                'endLine' => 35,
                'startTokenPos' => 113,
                'startFilePos' => 683,
                'endTokenPos' => 113,
                'endFilePos' => 686,
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 35,
            'endLine' => 35,
            'startColumn' => 9,
            'endColumn' => 29,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'email' => 
          array (
            'name' => 'email',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 36,
                'endLine' => 36,
                'startTokenPos' => 123,
                'startFilePos' => 714,
                'endTokenPos' => 123,
                'endFilePos' => 717,
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 36,
            'endLine' => 36,
            'startColumn' => 9,
            'endColumn' => 29,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'relationship' => 
          array (
            'name' => 'relationship',
            'default' => 
            array (
              'code' => '\'parent\'',
              'attributes' => 
              array (
                'startLine' => 37,
                'endLine' => 37,
                'startTokenPos' => 132,
                'startFilePos' => 751,
                'endTokenPos' => 132,
                'endFilePos' => 758,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 37,
            'endLine' => 37,
            'startColumn' => 9,
            'endColumn' => 39,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
          'isPrimary' => 
          array (
            'name' => 'isPrimary',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 38,
                'endLine' => 38,
                'startTokenPos' => 141,
                'startFilePos' => 787,
                'endTokenPos' => 141,
                'endFilePos' => 791,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 38,
            'endLine' => 38,
            'startColumn' => 9,
            'endColumn' => 31,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 33,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Domain\\ValueObjects',
        'declaringClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'implementingClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'currentClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'aliasName' => NULL,
      ),
      'display' => 
      array (
        'name' => 'display',
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
        'startLine' => 69,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Domain\\ValueObjects',
        'declaringClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'implementingClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'currentClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'aliasName' => NULL,
      ),
      'fromNullableGuardian' => 
      array (
        'name' => 'fromNullableGuardian',
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
            'startLine' => 79,
            'endLine' => 79,
            'startColumn' => 49,
            'endColumn' => 59,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
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
                  'name' => 'self',
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
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Builds a reference from a raw form array, or null when the row is empty.
 *
 * @param  array<string, mixed>  $data
 */',
        'startLine' => 79,
        'endLine' => 94,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Domains\\Student\\Domain\\ValueObjects',
        'declaringClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'implementingClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'currentClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'aliasName' => NULL,
      ),
      'toArray' => 
      array (
        'name' => 'toArray',
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
        'startLine' => 96,
        'endLine' => 105,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Domain\\ValueObjects',
        'declaringClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'implementingClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        'currentClassName' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
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