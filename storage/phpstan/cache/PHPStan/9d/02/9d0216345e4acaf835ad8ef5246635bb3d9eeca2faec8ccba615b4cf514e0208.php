<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\NexusSchool\app\Domains\Library\Services\CirculationService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domains\Library\Services\CirculationService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-2cdc0015886b7886664a228333e74cd4007bf5300b21691daa10c27d55e677a6',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domains\\Library\\Services\\CirculationService',
        'filename' => 'C:/xampp/htdocs/NexusSchool/app/Domains/Library/Services/CirculationService.php',
      ),
    ),
    'namespace' => 'App\\Domains\\Library\\Services',
    'name' => 'App\\Domains\\Library\\Services\\CirculationService',
    'shortName' => 'CirculationService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 27,
    'endLine' => 401,
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
      'loanModel' => 
      array (
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'name' => 'loanModel',
        'modifiers' => 2,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Models\\BookLoan',
            'isIdentifier' => false,
          ),
        ),
        'default' => 
        array (
          'code' => 'new \\App\\Models\\BookLoan()',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 131,
            'startFilePos' => 974,
            'endTokenPos' => 135,
            'endFilePos' => 987,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 9,
        'endColumn' => 54,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'reservationModel' => 
      array (
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'name' => 'reservationModel',
        'modifiers' => 2,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Models\\Reservation',
            'isIdentifier' => false,
          ),
        ),
        'default' => 
        array (
          'code' => 'new \\App\\Models\\Reservation()',
          'attributes' => 
          array (
            'startLine' => 31,
            'endLine' => 31,
            'startTokenPos' => 146,
            'startFilePos' => 1040,
            'endTokenPos' => 150,
            'endFilePos' => 1056,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 9,
        'endColumn' => 67,
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
          'loanModel' => 
          array (
            'name' => 'loanModel',
            'default' => 
            array (
              'code' => 'new \\App\\Models\\BookLoan()',
              'attributes' => 
              array (
                'startLine' => 30,
                'endLine' => 30,
                'startTokenPos' => 131,
                'startFilePos' => 974,
                'endTokenPos' => 135,
                'endFilePos' => 987,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\BookLoan',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 30,
            'endLine' => 30,
            'startColumn' => 9,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'reservationModel' => 
          array (
            'name' => 'reservationModel',
            'default' => 
            array (
              'code' => 'new \\App\\Models\\Reservation()',
              'attributes' => 
              array (
                'startLine' => 31,
                'endLine' => 31,
                'startTokenPos' => 146,
                'startFilePos' => 1040,
                'endTokenPos' => 150,
                'endFilePos' => 1056,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Reservation',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 31,
            'endLine' => 31,
            'startColumn' => 9,
            'endColumn' => 67,
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
        'startLine' => 29,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'borrowBook' => 
      array (
        'name' => 'borrowBook',
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
                'name' => 'App\\Domains\\Library\\DTOs\\BookLoanData',
                'isIdentifier' => false,
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
            'startColumn' => 32,
            'endColumn' => 49,
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
            'name' => 'App\\Models\\BookLoan',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 41,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'returnBook' => 
      array (
        'name' => 'returnBook',
        'parameters' => 
        array (
          'loan' => 
          array (
            'name' => 'loan',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\BookLoan',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 71,
            'endLine' => 71,
            'startColumn' => 32,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'returnedTo' => 
          array (
            'name' => 'returnedTo',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 71,
                'endLine' => 71,
                'startTokenPos' => 445,
                'startFilePos' => 2597,
                'endTokenPos' => 445,
                'endFilePos' => 2600,
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
                      'name' => 'int',
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
            'startLine' => 71,
            'endLine' => 71,
            'startColumn' => 48,
            'endColumn' => 70,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Models\\BookLoan',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 71,
        'endLine' => 111,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'markLost' => 
      array (
        'name' => 'markLost',
        'parameters' => 
        array (
          'loan' => 
          array (
            'name' => 'loan',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\BookLoan',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 113,
            'endLine' => 113,
            'startColumn' => 30,
            'endColumn' => 43,
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
            'name' => 'App\\Models\\BookLoan',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 113,
        'endLine' => 129,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'renewLoan' => 
      array (
        'name' => 'renewLoan',
        'parameters' => 
        array (
          'loan' => 
          array (
            'name' => 'loan',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\BookLoan',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 131,
            'endLine' => 131,
            'startColumn' => 31,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'additionalDays' => 
          array (
            'name' => 'additionalDays',
            'default' => 
            array (
              'code' => '14',
              'attributes' => 
              array (
                'startLine' => 131,
                'endLine' => 131,
                'startTokenPos' => 846,
                'startFilePos' => 4593,
                'endTokenPos' => 846,
                'endFilePos' => 4594,
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
                      'name' => 'int',
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
            'startLine' => 131,
            'endLine' => 131,
            'startColumn' => 47,
            'endColumn' => 71,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Models\\BookLoan',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 131,
        'endLine' => 151,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'findLoanById' => 
      array (
        'name' => 'findLoanById',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 153,
            'endLine' => 153,
            'startColumn' => 34,
            'endColumn' => 40,
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
            'name' => 'App\\Models\\BookLoan',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 153,
        'endLine' => 156,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'paginateLoans' => 
      array (
        'name' => 'paginateLoans',
        'parameters' => 
        array (
          'filters' => 
          array (
            'name' => 'filters',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 158,
                'endLine' => 158,
                'startTokenPos' => 1026,
                'startFilePos' => 5435,
                'endTokenPos' => 1027,
                'endFilePos' => 5436,
              ),
            ),
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
            'startLine' => 158,
            'endLine' => 158,
            'startColumn' => 35,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'perPage' => 
          array (
            'name' => 'perPage',
            'default' => 
            array (
              'code' => '15',
              'attributes' => 
              array (
                'startLine' => 158,
                'endLine' => 158,
                'startTokenPos' => 1036,
                'startFilePos' => 5454,
                'endTokenPos' => 1036,
                'endFilePos' => 5455,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 158,
            'endLine' => 158,
            'startColumn' => 56,
            'endColumn' => 72,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Contracts\\Pagination\\LengthAwarePaginator',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 158,
        'endLine' => 188,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'getOverdueLoans' => 
      array (
        'name' => 'getOverdueLoans',
        'parameters' => 
        array (
          'schoolId' => 
          array (
            'name' => 'schoolId',
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
            'startLine' => 190,
            'endLine' => 190,
            'startColumn' => 37,
            'endColumn' => 52,
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
            'name' => 'Illuminate\\Database\\Eloquent\\Collection',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 190,
        'endLine' => 198,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'createReservation' => 
      array (
        'name' => 'createReservation',
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
                'name' => 'App\\Domains\\Library\\DTOs\\ReservationData',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 206,
            'endLine' => 206,
            'startColumn' => 39,
            'endColumn' => 59,
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
            'name' => 'App\\Models\\Reservation',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 206,
        'endLine' => 224,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'fulfillReservation' => 
      array (
        'name' => 'fulfillReservation',
        'parameters' => 
        array (
          'reservation' => 
          array (
            'name' => 'reservation',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Reservation',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 226,
            'endLine' => 226,
            'startColumn' => 40,
            'endColumn' => 63,
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
            'name' => 'App\\Models\\Reservation',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 226,
        'endLine' => 240,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'cancelReservation' => 
      array (
        'name' => 'cancelReservation',
        'parameters' => 
        array (
          'reservation' => 
          array (
            'name' => 'reservation',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Reservation',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 242,
            'endLine' => 242,
            'startColumn' => 39,
            'endColumn' => 62,
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
            'name' => 'App\\Models\\Reservation',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 242,
        'endLine' => 253,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'expireReservations' => 
      array (
        'name' => 'expireReservations',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 255,
        'endLine' => 273,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'findReservationById' => 
      array (
        'name' => 'findReservationById',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 275,
            'endLine' => 275,
            'startColumn' => 41,
            'endColumn' => 47,
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
            'name' => 'App\\Models\\Reservation',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 275,
        'endLine' => 278,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'paginateReservations' => 
      array (
        'name' => 'paginateReservations',
        'parameters' => 
        array (
          'filters' => 
          array (
            'name' => 'filters',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 280,
                'endLine' => 280,
                'startTokenPos' => 1927,
                'startFilePos' => 9594,
                'endTokenPos' => 1928,
                'endFilePos' => 9595,
              ),
            ),
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
            'startLine' => 280,
            'endLine' => 280,
            'startColumn' => 42,
            'endColumn' => 60,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'perPage' => 
          array (
            'name' => 'perPage',
            'default' => 
            array (
              'code' => '15',
              'attributes' => 
              array (
                'startLine' => 280,
                'endLine' => 280,
                'startTokenPos' => 1937,
                'startFilePos' => 9613,
                'endTokenPos' => 1937,
                'endFilePos' => 9614,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 280,
            'endLine' => 280,
            'startColumn' => 63,
            'endColumn' => 79,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Contracts\\Pagination\\LengthAwarePaginator',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 280,
        'endLine' => 302,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'validateCopyAvailable' => 
      array (
        'name' => 'validateCopyAvailable',
        'parameters' => 
        array (
          'bookCopyId' => 
          array (
            'name' => 'bookCopyId',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 310,
            'endLine' => 310,
            'startColumn' => 44,
            'endColumn' => 58,
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
        'startLine' => 310,
        'endLine' => 325,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'validateNoDuplicateLoan' => 
      array (
        'name' => 'validateNoDuplicateLoan',
        'parameters' => 
        array (
          'bookCopyId' => 
          array (
            'name' => 'bookCopyId',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 327,
            'endLine' => 327,
            'startColumn' => 46,
            'endColumn' => 60,
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
        'startLine' => 327,
        'endLine' => 339,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'validateBorrowerNotAtLimit' => 
      array (
        'name' => 'validateBorrowerNotAtLimit',
        'parameters' => 
        array (
          'borrowerId' => 
          array (
            'name' => 'borrowerId',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 341,
            'endLine' => 341,
            'startColumn' => 49,
            'endColumn' => 63,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'borrowerType' => 
          array (
            'name' => 'borrowerType',
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
            'startLine' => 341,
            'endLine' => 341,
            'startColumn' => 66,
            'endColumn' => 85,
            'parameterIndex' => 1,
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
        'startLine' => 341,
        'endLine' => 356,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'validateNoDuplicatePendingReservation' => 
      array (
        'name' => 'validateNoDuplicatePendingReservation',
        'parameters' => 
        array (
          'bookId' => 
          array (
            'name' => 'bookId',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 358,
            'endLine' => 358,
            'startColumn' => 60,
            'endColumn' => 70,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'borrowerId' => 
          array (
            'name' => 'borrowerId',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 358,
            'endLine' => 358,
            'startColumn' => 73,
            'endColumn' => 87,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'borrowerType' => 
          array (
            'name' => 'borrowerType',
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
            'startLine' => 358,
            'endLine' => 358,
            'startColumn' => 90,
            'endColumn' => 109,
            'parameterIndex' => 2,
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
        'startLine' => 358,
        'endLine' => 372,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'validateBookExists' => 
      array (
        'name' => 'validateBookExists',
        'parameters' => 
        array (
          'bookId' => 
          array (
            'name' => 'bookId',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 374,
            'endLine' => 374,
            'startColumn' => 41,
            'endColumn' => 51,
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
        'startLine' => 374,
        'endLine' => 383,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'aliasName' => NULL,
      ),
      'fulfillPendingReservation' => 
      array (
        'name' => 'fulfillPendingReservation',
        'parameters' => 
        array (
          'bookId' => 
          array (
            'name' => 'bookId',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 385,
            'endLine' => 385,
            'startColumn' => 48,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'borrowerId' => 
          array (
            'name' => 'borrowerId',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 385,
            'endLine' => 385,
            'startColumn' => 61,
            'endColumn' => 75,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'borrowerType' => 
          array (
            'name' => 'borrowerType',
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
            'startLine' => 385,
            'endLine' => 385,
            'startColumn' => 78,
            'endColumn' => 97,
            'parameterIndex' => 2,
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
        'startLine' => 385,
        'endLine' => 400,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Domains\\Library\\Services',
        'declaringClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'implementingClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
        'currentClassName' => 'App\\Domains\\Library\\Services\\CirculationService',
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