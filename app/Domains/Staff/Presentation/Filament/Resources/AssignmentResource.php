<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources;

use App\Domains\Staff\Domain\Enums\AssignmentType;
use App\Domains\Staff\Infrastructure\Models\Assignment;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Domains\Staff\Presentation\Filament\Resources\AssignmentResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AssignmentResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = Assignment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static string|\UnitEnum|null $navigationGroup = 'Ressources humaines';

    protected static ?string $navigationLabel = 'Affectations';

    protected static ?string $modelLabel = 'Affectation';

    protected static ?string $pluralModelLabel = 'Affectations';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Affectation')
                    ->schema([
                        Forms\Components\Select::make('staff_id')->label('Membre du personnel')
                            ->relationship('staff', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn (Staff $record): string => $record->full_name)
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('assignment_type')->label('Type')
                            ->options(AssignmentType::class)->required()
                            ->reactive(),
                        Forms\Components\Select::make('department_id')->label('Département')
                            ->relationship('department', 'name')->searchable()->preload()
                            ->visible(fn (Get $get): bool => $get('assignment_type') === AssignmentType::Department->value),
                        Forms\Components\Select::make('position_id')->label('Poste')
                            ->relationship('position', 'title')->searchable()->preload()
                            ->visible(fn (Get $get): bool => $get('assignment_type') === AssignmentType::Position->value),
                        Forms\Components\TextInput::make('campus_school_id')->label('Campus')
                            ->visible(fn (Get $get): bool => $get('assignment_type') === AssignmentType::Campus->value),
                        Forms\Components\Select::make('employment_id')->label('Contrat')
                            ->relationship('employment', 'id')->searchable()->preload(),
                        Forms\Components\DatePicker::make('assignment_start_date')->label('Début')->required(),
                        Forms\Components\DatePicker::make('assignment_end_date')->label('Fin'),
                        Forms\Components\Toggle::make('is_current')->label('En cours')->default(true),
                    ])->columns(3),
                Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')->label('Notes')->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('staff.full_name')->label('Membre du personnel')->searchable(),
                Tables\Columns\TextColumn::make('assignment_type')->label('Type')
                    ->badge()->color('info'),
                Tables\Columns\TextColumn::make('department.name')->label('Département')->placeholder('—'),
                Tables\Columns\TextColumn::make('position.title')->label('Poste')->placeholder('—'),
                Tables\Columns\TextColumn::make('assignment_start_date')->label('Début')->date()->sortable(),
                Tables\Columns\TextColumn::make('assignment_end_date')->label('Fin')->date()->placeholder('—'),
                Tables\Columns\IconColumn::make('is_current')->label('En cours')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('assignment_type')->label('Type')->options(AssignmentType::class),
            ])
            ->actions([
                EditAction::make()->label('Modifier'),
                DeleteAction::make()->label('Supprimer'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssignments::route('/'),
            'create' => Pages\CreateAssignment::route('/create'),
            'edit' => Pages\EditAssignment::route('/{record}/edit'),
        ];
    }

    public static function canDelete($record = null): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
