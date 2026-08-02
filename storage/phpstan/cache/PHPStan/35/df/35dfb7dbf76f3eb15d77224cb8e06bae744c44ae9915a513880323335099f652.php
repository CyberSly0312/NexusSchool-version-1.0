<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\NexusSchool\app\Domains\Student\Infrastructure\Models\Student.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domains\Student\Infrastructure\Models\Student
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-cb49135cad998bf4b7bda54ea6a1f99000f725e737cf3b132f9605306d6e1eca',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'filename' => 'C:/xampp/htdocs/NexusSchool/app/Domains/Student/Infrastructure/Models/Student.php',
      ),
    ),
    'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
    'name' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
    'shortName' => 'Student',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Aggregate root of the Student domain (SPR-003).
 *
 * Invariants are enforced by the application actions, never by the model
 * itself: a unique student number per school, an optional but unique email,
 * a non-future birth date, an admission date inside the assigned academic
 * year, and a single current academic assignment.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 31,
    'endLine' => 229,
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
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'uuid\', \'school_id\', \'student_number\', \'admission_number\', \'national_id\', \'birth_certificate_reference\', \'first_name\', \'last_name\', \'middle_name\', \'preferred_name\', \'gender\', \'date_of_birth\', \'place_of_birth\', \'nationality\', \'blood_group\', \'religion\', \'photo\', \'email\', \'phone_number\', \'residential_address\', \'address_street\', \'address_city\', \'address_state\', \'address_postal_code\', \'address_country\', \'admission_date\', \'academic_calendar_year_id\', \'school_class_id\', \'class_section_id\', \'allergies\', \'medical_notes\', \'emergency_contact_name\', \'emergency_contact_phone\', \'emergency_contact_relationship\', \'user_id\', \'notes\', \'status\', \'transfer_status\', \'transferred_at\', \'transfer_from_school\', \'transfer_to_school\', \'graduated_at\', \'suspended_at\', \'suspended_until\', \'suspension_reason\', \'withdrawn_at\', \'withdrawal_reason\', \'reactivated_at\', \'archived_at\']',
          'attributes' => 
          array (
            'startLine' => 37,
            'endLine' => 87,
            'startTokenPos' => 123,
            'startFilePos' => 1272,
            'endTokenPos' => 272,
            'endFilePos' => 2530,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 87,
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
        'startLine' => 89,
        'endLine' => 105,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'aliasName' => NULL,
      ),
      'newFactory' => 
      array (
        'name' => 'newFactory',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 107,
        'endLine' => 110,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'aliasName' => NULL,
      ),
      'getFullNameAttribute' => 
      array (
        'name' => 'getFullNameAttribute',
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
        'startLine' => 118,
        'endLine' => 121,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
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
        'startLine' => 129,
        'endLine' => 132,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'aliasName' => NULL,
      ),
      'user' => 
      array (
        'name' => 'user',
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
        'startLine' => 134,
        'endLine' => 137,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'aliasName' => NULL,
      ),
      'academicCalendarYear' => 
      array (
        'name' => 'academicCalendarYear',
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
        'startLine' => 139,
        'endLine' => 142,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
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
        'startLine' => 144,
        'endLine' => 147,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'aliasName' => NULL,
      ),
      'section' => 
      array (
        'name' => 'section',
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
        'startLine' => 149,
        'endLine' => 152,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'aliasName' => NULL,
      ),
      'guardians' => 
      array (
        'name' => 'guardians',
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
        'startLine' => 154,
        'endLine' => 157,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'aliasName' => NULL,
      ),
      'scopeActive' => 
      array (
        'name' => 'scopeActive',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 165,
            'endLine' => 165,
            'startColumn' => 33,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 165,
        'endLine' => 168,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'aliasName' => NULL,
      ),
      'scopeArchived' => 
      array (
        'name' => 'scopeArchived',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 170,
            'endLine' => 170,
            'startColumn' => 35,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 170,
        'endLine' => 173,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'aliasName' => NULL,
      ),
      'scopeByStatus' => 
      array (
        'name' => 'scopeByStatus',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 175,
            'endLine' => 175,
            'startColumn' => 35,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'status' => 
          array (
            'name' => 'status',
            'default' => NULL,
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
                      'name' => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
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
            'startLine' => 175,
            'endLine' => 175,
            'startColumn' => 43,
            'endColumn' => 70,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 175,
        'endLine' => 178,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'aliasName' => NULL,
      ),
      'isActive' => 
      array (
        'name' => 'isActive',
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
        'startLine' => 186,
        'endLine' => 189,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'aliasName' => NULL,
      ),
      'isArchived' => 
      array (
        'name' => 'isArchived',
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
        'startLine' => 191,
        'endLine' => 194,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'aliasName' => NULL,
      ),
      'isGraduated' => 
      array (
        'name' => 'isGraduated',
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
        'startLine' => 196,
        'endLine' => 199,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'aliasName' => NULL,
      ),
      'isSuspended' => 
      array (
        'name' => 'isSuspended',
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
        'startLine' => 201,
        'endLine' => 204,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'aliasName' => NULL,
      ),
      'isWithdrawn' => 
      array (
        'name' => 'isWithdrawn',
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
        'startLine' => 206,
        'endLine' => 209,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'aliasName' => NULL,
      ),
      'isInactive' => 
      array (
        'name' => 'isInactive',
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
        'startLine' => 211,
        'endLine' => 214,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'aliasName' => NULL,
      ),
      'isImmutable' => 
      array (
        'name' => 'isImmutable',
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
        'startLine' => 216,
        'endLine' => 219,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'aliasName' => NULL,
      ),
      'displayName' => 
      array (
        'name' => 'displayName',
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
        'startLine' => 221,
        'endLine' => 228,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
        'declaringClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'implementingClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        'currentClassName' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
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