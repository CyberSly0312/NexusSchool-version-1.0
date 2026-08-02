<?php

declare(strict_types=1);

namespace App\Domains\Academic;

use App\Domains\Academic\Application\Services\AcademicStructureService;
use App\Domains\Academic\Application\Services\CalendarService;
use App\Domains\Academic\Application\Services\SubjectCatalogService;
use App\Domains\Academic\Domain\Contracts\CalendarYearRepositoryInterface;
use App\Domains\Academic\Domain\Events\CalendarTermCreated;
use App\Domains\Academic\Domain\Events\CalendarYearActivated;
use App\Domains\Academic\Domain\Events\CalendarYearArchived;
use App\Domains\Academic\Domain\Events\CalendarYearCreated;
use App\Domains\Academic\Domain\Events\ClassSectionCreated;
use App\Domains\Academic\Domain\Events\SchoolClassCreated;
use App\Domains\Academic\Domain\Events\StreamCreated;
use App\Domains\Academic\Domain\Events\SubjectCreated;
use App\Domains\Academic\Infrastructure\Listeners\AcademicAuditLogger;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarEvent;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\AcademicProgram;
use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\Coefficient;
use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Academic\Infrastructure\Models\Stream;
use App\Domains\Academic\Infrastructure\Models\SubjectGroup;
use App\Domains\Academic\Infrastructure\Observers\AcademicEntityObserver;
use App\Domains\Academic\Infrastructure\Policies\AcademicCalendarEventPolicy;
use App\Domains\Academic\Infrastructure\Policies\AcademicCalendarTermPolicy;
use App\Domains\Academic\Infrastructure\Policies\AcademicCalendarYearPolicy;
use App\Domains\Academic\Infrastructure\Policies\AcademicProgramPolicy;
use App\Domains\Academic\Infrastructure\Policies\AcademicSubjectPolicy;
use App\Domains\Academic\Infrastructure\Policies\ClassSectionPolicy;
use App\Domains\Academic\Infrastructure\Policies\CoefficientPolicy;
use App\Domains\Academic\Infrastructure\Policies\EducationLevelPolicy;
use App\Domains\Academic\Infrastructure\Policies\SchoolClassPolicy;
use App\Domains\Academic\Infrastructure\Policies\StreamPolicy;
use App\Domains\Academic\Infrastructure\Policies\SubjectGroupPolicy;
use App\Domains\Academic\Infrastructure\Repositories\EloquentCalendarYearRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class AcademicServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CalendarYearRepositoryInterface::class, EloquentCalendarYearRepository::class);

        $this->app->singleton(CalendarService::class);
        $this->app->singleton(AcademicStructureService::class);
        $this->app->singleton(SubjectCatalogService::class);
    }

    public function boot(): void
    {
        $this->registerObservers();
        $this->registerPolicies();
        $this->registerEvents();
    }

    private function registerObservers(): void
    {
        $observer = AcademicEntityObserver::class;

        AcademicCalendarYear::observe($observer);
        AcademicCalendarTerm::observe($observer);
        AcademicCalendarEvent::observe($observer);
        EducationLevel::observe($observer);
        AcademicProgram::observe($observer);
        Stream::observe($observer);
        SchoolClass::observe($observer);
        ClassSection::observe($observer);
        SubjectGroup::observe($observer);
        AcademicSubject::observe($observer);
        Coefficient::observe($observer);
    }

    private function registerPolicies(): void
    {
        Gate::policy(AcademicCalendarYear::class, AcademicCalendarYearPolicy::class);
        Gate::policy(AcademicCalendarTerm::class, AcademicCalendarTermPolicy::class);
        Gate::policy(AcademicCalendarEvent::class, AcademicCalendarEventPolicy::class);
        Gate::policy(EducationLevel::class, EducationLevelPolicy::class);
        Gate::policy(AcademicProgram::class, AcademicProgramPolicy::class);
        Gate::policy(Stream::class, StreamPolicy::class);
        Gate::policy(SchoolClass::class, SchoolClassPolicy::class);
        Gate::policy(ClassSection::class, ClassSectionPolicy::class);
        Gate::policy(SubjectGroup::class, SubjectGroupPolicy::class);
        Gate::policy(AcademicSubject::class, AcademicSubjectPolicy::class);
        Gate::policy(Coefficient::class, CoefficientPolicy::class);
    }

    private function registerEvents(): void
    {
        Event::listen(CalendarYearCreated::class, [AcademicAuditLogger::class, 'handleCalendarYearCreated']);
        Event::listen(CalendarYearActivated::class, [AcademicAuditLogger::class, 'handleCalendarYearActivated']);
        Event::listen(CalendarYearArchived::class, [AcademicAuditLogger::class, 'handleCalendarYearArchived']);
        Event::listen(CalendarTermCreated::class, [AcademicAuditLogger::class, 'handleCalendarTermCreated']);
        Event::listen(StreamCreated::class, [AcademicAuditLogger::class, 'handleStreamCreated']);
        Event::listen(SchoolClassCreated::class, [AcademicAuditLogger::class, 'handleSchoolClassCreated']);
        Event::listen(ClassSectionCreated::class, [AcademicAuditLogger::class, 'handleClassSectionCreated']);
        Event::listen(SubjectCreated::class, [AcademicAuditLogger::class, 'handleSubjectCreated']);
    }
}
