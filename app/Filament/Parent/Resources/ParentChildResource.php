<?php

declare(strict_types=1);

namespace App\Filament\Parent\Resources;

use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Guardian\Application\Services\GuardianChildAccessService;
use App\Domains\Guardian\Domain\Enums\GuardianChildOperation;
use App\Domains\Guardian\Domain\Exceptions\GuardianChildAccessDeniedException;
use App\Domains\Student\Domain\Enums\StudentStatus;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Filament\Parent\Resources\ParentChildResource\Pages;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Minimal read-only Parent surface. Every record is resolved through the
 * centralized child-level authorization service, including crafted record IDs
 * sent directly to the view route.
 */
final class ParentChildResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = 'Mes enfants';

    protected static ?string $modelLabel = 'Enfant';

    protected static ?string $pluralModelLabel = 'Enfants';

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?int $navigationSort = 10;

    /**
     * Parent authorization is derived only from centralized child-level rules.
     * The global StudentPolicy is intentionally bypassed: Parent access is not
     * a staff permission and no role allowlist is expanded.
     */
    public static function getViewAnyAuthorizationResponse(): Response
    {
        try {
            app(GuardianChildAccessService::class)->assertParentContext();

            return Response::allow();
        } catch (GuardianChildAccessDeniedException) {
            return Response::deny();
        }
    }

    public static function getViewAuthorizationResponse(Model $record): Response
    {
        if (! $record instanceof Student) {
            return Response::deny();
        }

        try {
            app(GuardianChildAccessService::class)->assertCanAccess(
                $record->getKey(),
                GuardianChildOperation::View,
            );

            return Response::allow();
        } catch (GuardianChildAccessDeniedException) {
            return Response::deny();
        }
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Placeholder::make('full_name')
                    ->label('Nom complet')
                    ->content(fn (Student $record): string => $record->displayName()),
                Forms\Components\TextInput::make('student_number')
                    ->label('Numéro élève')
                    ->disabled(),
                Forms\Components\TextInput::make('admission_date')
                    ->label('Date d’admission')
                    ->disabled(),
                Placeholder::make('class')
                    ->label('Classe')
                    ->content(fn (Student $record): string => SchoolClass::query()
                        ->forSchool($record->school_id)
                        ->whereKey($record->school_class_id)
                        ->value('name') ?? '—'),
                Forms\Components\Select::make('status')
                    ->label('Statut')
                    ->options(StudentStatus::class)
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nom complet')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('student_number')
                    ->label('Numéro élève')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('schoolClass.name')
                    ->label('Classe')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (StudentStatus $state): string => $state->color()),
            ])
            ->actions([
                ViewAction::make()->label('Consulter'),
            ])
            ->bulkActions([])
            ->defaultPaginationPageOption(10);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(GuardianChildAccessService::class)->scopeStudentQuery(
            parent::getEloquentQuery(),
            GuardianChildOperation::View,
        );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParentChildren::route('/'),
            'view' => Pages\ViewParentChild::route('/{record}'),
        ];
    }
}
