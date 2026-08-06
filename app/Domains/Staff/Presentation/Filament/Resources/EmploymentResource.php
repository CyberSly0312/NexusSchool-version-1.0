<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources;

use App\Domains\Staff\Domain\Enums\ContractType;
use App\Domains\Staff\Domain\Enums\EmploymentStatus;
use App\Domains\Staff\Domain\Enums\EmploymentType;
use App\Domains\Staff\Infrastructure\Models\Employment;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Domains\Staff\Presentation\Filament\Resources\EmploymentResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class EmploymentResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = Employment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Ressources humaines';

    protected static ?string $navigationLabel = 'Contrats';

    protected static ?string $modelLabel = 'Contrat';

    protected static ?string $pluralModelLabel = 'Contrats';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Contrat')
                    ->schema([
                        Forms\Components\Select::make('staff_id')->label('Membre du personnel')
                            ->relationship('staff', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn (Staff $record): string => $record->full_name)
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('position_id')->label('Poste')
                            ->relationship('position', 'title')->searchable()->preload(),
                        Forms\Components\Select::make('department_id')->label('Département')
                            ->relationship('department', 'name')->searchable()->preload(),
                        Forms\Components\Select::make('employment_type')->label('Type')
                            ->options(EmploymentType::class)->required(),
                        Forms\Components\Select::make('contract_type')->label('Nature du contrat')
                            ->options(ContractType::class)->required(),
                        Forms\Components\Select::make('status')->label('Statut')
                            ->options(EmploymentStatus::class)->required(),
                        Forms\Components\TextInput::make('salary_scale')->label('Grille salariale')->maxLength(20),
                        Forms\Components\TextInput::make('salary_amount')->label('Salaire')->numeric(),
                    ])->columns(3),
                Section::make('Période')
                    ->schema([
                        Forms\Components\DatePicker::make('contract_start_date')->label('Début du contrat')->required(),
                        Forms\Components\DatePicker::make('contract_end_date')->label('Fin du contrat'),
                        Forms\Components\DatePicker::make('termination_date')->label('Date de fin réelle'),
                        Forms\Components\Textarea::make('termination_reason')->label('Motif de fin')->rows(2),
                    ])->columns(2),
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
                Tables\Columns\TextColumn::make('staff.staff_number')->label('N° matricule')->searchable(),
                Tables\Columns\TextColumn::make('staff.full_name')->label('Membre du personnel')->searchable(),
                Tables\Columns\TextColumn::make('position.title')->label('Poste')->placeholder('—'),
                Tables\Columns\TextColumn::make('employment_type')->label('Type')
                    ->badge()->color('info'),
                Tables\Columns\TextColumn::make('contract_start_date')->label('Début')->date()->sortable(),
                Tables\Columns\TextColumn::make('contract_end_date')->label('Fin')->date()->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->label('Statut')
                    ->badge()
                    ->color(fn (EmploymentStatus $state): string => $state->color()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Statut')->options(EmploymentStatus::class),
            ])
            ->actions([
                EditAction::make()->label('Modifier'),
                DeleteAction::make()->label('Supprimer'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployments::route('/'),
            'create' => Pages\CreateEmployment::route('/create'),
            'edit' => Pages\EditEmployment::route('/{record}/edit'),
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
