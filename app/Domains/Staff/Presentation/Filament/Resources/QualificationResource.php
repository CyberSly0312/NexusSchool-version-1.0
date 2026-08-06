<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources;

use App\Domains\Staff\Domain\Enums\QualificationLevel;
use App\Domains\Staff\Infrastructure\Models\Qualification;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Domains\Staff\Presentation\Filament\Resources\QualificationResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class QualificationResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = Qualification::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'Ressources humaines';

    protected static ?string $navigationLabel = 'Qualifications';

    protected static ?string $modelLabel = 'Qualification';

    protected static ?string $pluralModelLabel = 'Qualifications';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Qualification')
                    ->schema([
                        Forms\Components\Select::make('staff_id')->label('Membre du personnel')
                            ->relationship('staff', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn (Staff $record): string => $record->full_name)
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('qualification_level')->label('Niveau')
                            ->options(QualificationLevel::class)->required(),
                        Forms\Components\TextInput::make('field_of_study')->label('Domaine')->maxLength(150),
                        Forms\Components\TextInput::make('institution')->label('Institution')->maxLength(200),
                        Forms\Components\DatePicker::make('qualification_date')->label('Date'),
                        Forms\Components\TextInput::make('grade')->label('Mention')->maxLength(50),
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
                Tables\Columns\TextColumn::make('qualification_level')->label('Niveau')
                    ->badge()->color('info'),
                Tables\Columns\TextColumn::make('field_of_study')->label('Domaine')->placeholder('—'),
                Tables\Columns\TextColumn::make('institution')->label('Institution'),
                Tables\Columns\TextColumn::make('qualification_date')->label('Date')->date(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('qualification_level')->label('Niveau')->options(QualificationLevel::class),
            ])
            ->actions([
                EditAction::make()->label('Modifier'),
                DeleteAction::make()->label('Supprimer'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQualifications::route('/'),
            'create' => Pages\CreateQualification::route('/create'),
            'edit' => Pages\EditQualification::route('/{record}/edit'),
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
