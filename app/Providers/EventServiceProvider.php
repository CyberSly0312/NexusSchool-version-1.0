<?php

namespace App\Providers;

use App\Domains\AcademicClass\Events\AcademicClassActivated;
use App\Domains\AcademicClass\Events\AcademicClassArchived;
use App\Domains\AcademicClass\Events\AcademicClassCreated;
use App\Domains\AcademicClass\Events\AcademicClassUpdated;
use App\Domains\AcademicClass\Listeners\LogAcademicClassActivated;
use App\Domains\AcademicClass\Listeners\LogAcademicClassArchived;
use App\Domains\AcademicClass\Listeners\LogAcademicClassCreated;
use App\Domains\AcademicClass\Listeners\LogAcademicClassUpdated;
use App\Domains\AcademicTerm\Events\AcademicTermActivated;
use App\Domains\AcademicTerm\Events\AcademicTermArchived;
use App\Domains\AcademicTerm\Events\AcademicTermCreated;
use App\Domains\AcademicTerm\Events\AcademicTermStatusChanged;
use App\Domains\AcademicTerm\Events\AcademicTermUpdated;
use App\Domains\AcademicTerm\Listeners\LogAcademicTermActivated;
use App\Domains\AcademicTerm\Listeners\LogAcademicTermArchived;
use App\Domains\AcademicTerm\Listeners\LogAcademicTermCreated;
use App\Domains\AcademicTerm\Listeners\LogAcademicTermStatusChanged;
use App\Domains\AcademicTerm\Listeners\LogAcademicTermUpdated;
use App\Domains\AcademicYear\Events\AcademicYearActivated;
use App\Domains\AcademicYear\Events\AcademicYearArchived;
use App\Domains\AcademicYear\Events\AcademicYearCloned;
use App\Domains\AcademicYear\Events\AcademicYearCreated;
use App\Domains\AcademicYear\Events\AcademicYearDatesChanged;
use App\Domains\AcademicYear\Events\AcademicYearDeleted;
use App\Domains\AcademicYear\Events\AcademicYearStatusChanged;
use App\Domains\AcademicYear\Events\AcademicYearSwitched;
use App\Domains\AcademicYear\Events\AcademicYearUpdated;
use App\Domains\AcademicYear\Listeners\LogAcademicYearActivated;
use App\Domains\AcademicYear\Listeners\LogAcademicYearArchived;
use App\Domains\AcademicYear\Listeners\LogAcademicYearCloned;
use App\Domains\AcademicYear\Listeners\LogAcademicYearCreated;
use App\Domains\AcademicYear\Listeners\LogAcademicYearDatesChanged;
use App\Domains\AcademicYear\Listeners\LogAcademicYearDeleted;
use App\Domains\AcademicYear\Listeners\LogAcademicYearStatusChanged;
use App\Domains\AcademicYear\Listeners\LogAcademicYearSwitched;
use App\Domains\AcademicYear\Listeners\LogAcademicYearUpdated;
use App\Domains\Classroom\Events\ClassroomAssigned;
use App\Domains\Classroom\Events\ClassroomCreated;
use App\Domains\Classroom\Events\ClassroomReleased;
use App\Domains\Classroom\Events\ClassroomUpdated;
use App\Domains\Classroom\Listeners\LogClassroomAssigned;
use App\Domains\Classroom\Listeners\LogClassroomCreated;
use App\Domains\Classroom\Listeners\LogClassroomReleased;
use App\Domains\Classroom\Listeners\LogClassroomUpdated;
use App\Domains\Department\Events\DepartmentArchived;
use App\Domains\Department\Events\DepartmentCreated;
use App\Domains\Department\Events\DepartmentUpdated;
use App\Domains\Department\Listeners\LogDepartmentArchived;
use App\Domains\Department\Listeners\LogDepartmentCreated;
use App\Domains\Department\Listeners\LogDepartmentUpdated;
use App\Domains\GradeLevel\Events\GradeLevelArchived;
use App\Domains\GradeLevel\Events\GradeLevelCreated;
use App\Domains\GradeLevel\Events\GradeLevelUpdated;
use App\Domains\GradeLevel\Listeners\LogGradeLevelArchived;
use App\Domains\GradeLevel\Listeners\LogGradeLevelCreated;
use App\Domains\GradeLevel\Listeners\LogGradeLevelUpdated;
use App\Domains\Guardian\Events\GuardianActivated;
use App\Domains\Guardian\Events\GuardianArchived;
use App\Domains\Guardian\Events\GuardianCreated;
use App\Domains\Guardian\Events\GuardianUpdated;
use App\Domains\Guardian\Listeners\LogGuardianActivated;
use App\Domains\Guardian\Listeners\LogGuardianArchived;
use App\Domains\Guardian\Listeners\LogGuardianCreated;
use App\Domains\Guardian\Listeners\LogGuardianUpdated;
use App\Domains\Permission\Events\PermissionArchived;
use App\Domains\Permission\Events\PermissionAssignedToRole;
use App\Domains\Permission\Events\PermissionCreated;
use App\Domains\Permission\Events\PermissionRevokedFromRole;
use App\Domains\Permission\Events\PermissionUpdated;
use App\Domains\Permission\Listeners\LogPermissionArchived;
use App\Domains\Permission\Listeners\LogPermissionAssignedToRole;
use App\Domains\Permission\Listeners\LogPermissionCreated;
use App\Domains\Permission\Listeners\LogPermissionRevokedFromRole;
use App\Domains\Permission\Listeners\LogPermissionUpdated;
use App\Domains\Program\Events\ProgramArchived;
use App\Domains\Program\Events\ProgramCreated;
use App\Domains\Program\Events\ProgramUpdated;
use App\Domains\Program\Listeners\LogProgramArchived;
use App\Domains\Program\Listeners\LogProgramCreated;
use App\Domains\Program\Listeners\LogProgramUpdated;
use App\Domains\Role\Events\RoleAssignedToUser;
use App\Domains\Role\Events\RoleCreated;
use App\Domains\Role\Events\RoleRevokedFromUser;
use App\Domains\Role\Events\RoleUpdated;
use App\Domains\Section\Events\SectionArchived;
use App\Domains\Section\Events\SectionCreated;
use App\Domains\Section\Events\SectionUpdated;
use App\Domains\Section\Listeners\LogSectionArchived;
use App\Domains\Section\Listeners\LogSectionCreated;
use App\Domains\Section\Listeners\LogSectionUpdated;
use App\Domains\Student\Events\StudentActivated;
use App\Domains\Student\Events\StudentArchived;
use App\Domains\Student\Events\StudentCreated;
use App\Domains\Student\Events\StudentUpdated;
use App\Domains\Student\Listeners\LogStudentActivated;
use App\Domains\Student\Listeners\LogStudentArchived;
use App\Domains\Student\Listeners\LogStudentCreated;
use App\Domains\Student\Listeners\LogStudentUpdated;
use App\Domains\StudentEnrollment\Events\EnrollmentActivated;
use App\Domains\StudentEnrollment\Events\EnrollmentCompleted;
use App\Domains\StudentEnrollment\Events\EnrollmentSuspended;
use App\Domains\StudentEnrollment\Events\EnrollmentWithdrawn;
use App\Domains\StudentEnrollment\Events\StudentEnrolled;
use App\Domains\StudentEnrollment\Listeners\LogEnrollmentActivated;
use App\Domains\StudentEnrollment\Listeners\LogEnrollmentCompleted;
use App\Domains\StudentEnrollment\Listeners\LogEnrollmentSuspended;
use App\Domains\StudentEnrollment\Listeners\LogEnrollmentWithdrawn;
use App\Domains\StudentEnrollment\Listeners\LogStudentEnrolled;
use App\Domains\Subject\Events\SubjectActivated;
use App\Domains\Subject\Events\SubjectArchived;
use App\Domains\Subject\Events\SubjectCreated;
use App\Domains\Subject\Events\SubjectUpdated;
use App\Domains\Subject\Listeners\LogSubjectActivated;
use App\Domains\Subject\Listeners\LogSubjectArchived;
use App\Domains\Subject\Listeners\LogSubjectCreated;
use App\Domains\Subject\Listeners\LogSubjectUpdated;
use App\Domains\SubjectCategory\Events\SubjectCategoryArchived;
use App\Domains\SubjectCategory\Events\SubjectCategoryCreated;
use App\Domains\SubjectCategory\Events\SubjectCategoryUpdated;
use App\Domains\SubjectCategory\Listeners\LogSubjectCategoryArchived;
use App\Domains\SubjectCategory\Listeners\LogSubjectCategoryCreated;
use App\Domains\SubjectCategory\Listeners\LogSubjectCategoryUpdated;
use App\Domains\Gradebook\Events\AssessmentResultRecorded;
use App\Domains\Gradebook\Events\AssessmentResultVerified;
use App\Domains\Gradebook\Events\AssessmentResultsPublished;
use App\Domains\Gradebook\Events\GradebookCalculated;
use App\Domains\Gradebook\Events\RankingsCalculated;
use App\Domains\Gradebook\Listeners\LogAssessmentResultRecorded;
use App\Domains\Gradebook\Listeners\LogAssessmentResultVerified;
use App\Domains\Gradebook\Listeners\LogAssessmentResultsPublished;
use App\Domains\Gradebook\Listeners\LogGradebookCalculated;
use App\Domains\Gradebook\Listeners\LogRankingsCalculated;
use App\Domains\SubjectCoefficient\Events\SubjectCoefficientActivated;
use App\Domains\SubjectCoefficient\Events\SubjectCoefficientArchived;
use App\Domains\SubjectCoefficient\Events\SubjectCoefficientCreated;
use App\Domains\SubjectCoefficient\Events\SubjectCoefficientUpdated;
use App\Domains\SubjectCoefficient\Listeners\LogSubjectCoefficientActivated;
use App\Domains\SubjectCoefficient\Listeners\LogSubjectCoefficientArchived;
use App\Domains\SubjectCoefficient\Listeners\LogSubjectCoefficientCreated;
use App\Domains\SubjectCoefficient\Listeners\LogSubjectCoefficientUpdated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        RoleCreated::class => [],
        RoleUpdated::class => [],
        RoleAssignedToUser::class => [],
        RoleRevokedFromUser::class => [],

        PermissionCreated::class => [
            LogPermissionCreated::class,
        ],
        PermissionUpdated::class => [
            LogPermissionUpdated::class,
        ],
        PermissionArchived::class => [
            LogPermissionArchived::class,
        ],
        PermissionAssignedToRole::class => [
            LogPermissionAssignedToRole::class,
        ],
        PermissionRevokedFromRole::class => [
            LogPermissionRevokedFromRole::class,
        ],

        AcademicYearCreated::class => [
            LogAcademicYearCreated::class,
        ],
        AcademicYearUpdated::class => [
            LogAcademicYearUpdated::class,
        ],
        AcademicYearDeleted::class => [
            LogAcademicYearDeleted::class,
        ],
        AcademicYearActivated::class => [
            LogAcademicYearActivated::class,
        ],
        AcademicYearArchived::class => [
            LogAcademicYearArchived::class,
        ],
        AcademicYearCloned::class => [
            LogAcademicYearCloned::class,
        ],
        AcademicYearDatesChanged::class => [
            LogAcademicYearDatesChanged::class,
        ],
        AcademicYearSwitched::class => [
            LogAcademicYearSwitched::class,
        ],
        AcademicYearStatusChanged::class => [
            LogAcademicYearStatusChanged::class,
        ],

        AcademicTermCreated::class => [
            LogAcademicTermCreated::class,
        ],
        AcademicTermUpdated::class => [
            LogAcademicTermUpdated::class,
        ],
        AcademicTermActivated::class => [
            LogAcademicTermActivated::class,
        ],
        AcademicTermArchived::class => [
            LogAcademicTermArchived::class,
        ],
        AcademicTermStatusChanged::class => [
            LogAcademicTermStatusChanged::class,
        ],

        DepartmentCreated::class => [
            LogDepartmentCreated::class,
        ],
        DepartmentUpdated::class => [
            LogDepartmentUpdated::class,
        ],
        DepartmentArchived::class => [
            LogDepartmentArchived::class,
        ],

        ProgramCreated::class => [
            LogProgramCreated::class,
        ],
        ProgramUpdated::class => [
            LogProgramUpdated::class,
        ],
        ProgramArchived::class => [
            LogProgramArchived::class,
        ],

        GradeLevelCreated::class => [
            LogGradeLevelCreated::class,
        ],
        GradeLevelUpdated::class => [
            LogGradeLevelUpdated::class,
        ],
        GradeLevelArchived::class => [
            LogGradeLevelArchived::class,
        ],

        SectionCreated::class => [
            LogSectionCreated::class,
        ],
        SectionUpdated::class => [
            LogSectionUpdated::class,
        ],
        SectionArchived::class => [
            LogSectionArchived::class,
        ],

        AcademicClassCreated::class => [
            LogAcademicClassCreated::class,
        ],
        AcademicClassUpdated::class => [
            LogAcademicClassUpdated::class,
        ],
        AcademicClassArchived::class => [
            LogAcademicClassArchived::class,
        ],
        AcademicClassActivated::class => [
            LogAcademicClassActivated::class,
        ],

        ClassroomCreated::class => [
            LogClassroomCreated::class,
        ],
        ClassroomUpdated::class => [
            LogClassroomUpdated::class,
        ],
        ClassroomArchived::class => [
            LogClassroomArchived::class,
        ],
        ClassroomAssigned::class => [
            LogClassroomAssigned::class,
        ],
        ClassroomReleased::class => [
            LogClassroomReleased::class,
        ],

        SubjectCategoryCreated::class => [
            LogSubjectCategoryCreated::class,
        ],
        SubjectCategoryUpdated::class => [
            LogSubjectCategoryUpdated::class,
        ],
        SubjectCategoryArchived::class => [
            LogSubjectCategoryArchived::class,
        ],

        SubjectCreated::class => [
            LogSubjectCreated::class,
        ],
        SubjectUpdated::class => [
            LogSubjectUpdated::class,
        ],
        SubjectArchived::class => [
            LogSubjectArchived::class,
        ],
        SubjectActivated::class => [
            LogSubjectActivated::class,
        ],
        SubjectArchived::class => [
            LogSubjectArchived::class,
        ],
        SubjectCreated::class => [
            LogSubjectCreated::class,
        ],
        SubjectUpdated::class => [
            LogSubjectUpdated::class,
        ],

        SubjectCoefficientCreated::class => [
            LogSubjectCoefficientCreated::class,
        ],
        SubjectCoefficientUpdated::class => [
            LogSubjectCoefficientUpdated::class,
        ],
        SubjectCoefficientArchived::class => [
            LogSubjectCoefficientArchived::class,
        ],
        SubjectCoefficientActivated::class => [
            LogSubjectCoefficientActivated::class,
        ],

        GuardianCreated::class => [
            LogGuardianCreated::class,
        ],
        GuardianUpdated::class => [
            LogGuardianUpdated::class,
        ],
        GuardianArchived::class => [
            LogGuardianArchived::class,
        ],
        GuardianActivated::class => [
            LogGuardianActivated::class,
        ],

        StudentCreated::class => [
            LogStudentCreated::class,
        ],
        StudentUpdated::class => [
            LogStudentUpdated::class,
        ],
        StudentArchived::class => [
            LogStudentArchived::class,
        ],
        StudentActivated::class => [
            LogStudentActivated::class,
        ],

        StudentEnrolled::class => [
            LogStudentEnrolled::class,
        ],
        EnrollmentActivated::class => [
            LogEnrollmentActivated::class,
        ],
        EnrollmentSuspended::class => [
            LogEnrollmentSuspended::class,
        ],
        EnrollmentCompleted::class => [
            LogEnrollmentCompleted::class,
        ],
        EnrollmentWithdrawn::class => [
            LogEnrollmentWithdrawn::class,
        ],

        AssessmentResultRecorded::class => [
            LogAssessmentResultRecorded::class,
        ],
        AssessmentResultVerified::class => [
            LogAssessmentResultVerified::class,
        ],
        AssessmentResultsPublished::class => [
            LogAssessmentResultsPublished::class,
        ],
        GradebookCalculated::class => [
            LogGradebookCalculated::class,
        ],
        RankingsCalculated::class => [
            LogRankingsCalculated::class,
        ],
    ];
}
