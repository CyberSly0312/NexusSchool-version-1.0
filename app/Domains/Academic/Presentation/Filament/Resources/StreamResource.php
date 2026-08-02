<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources;

use App\Domains\Academic\Domain\Enums\EntityStatus;
use App\Domains\Academic\Infrastructure\Models\AcademicProgram;
use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\Academic\Infrastructure\Models\Stream;
use App\Domains\Academic\Presentation\Filament\Resources\StreamResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class StreamResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = Stream::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'Académique';

    protected static ?string $navigationLabel = 'Filières';

    protected static ?string $modelLabel = 'Filière';

    protected static ?string $pluralModelLabel = 'Filières';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 60;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Détails de la filière')
                    ->schema([
                        Forms\Components\Select::make('academic_program_id')
                            ->label('Programme')
                            ->options(fn (): array => AcademicProgram::query()->forSchool(tenant()?->id ?? '')->pluck('name', 'id')->all())
                            ->required(),
                        Forms\Components\Select::make('education_level_id')
                            ->label('Niveau')
                            ->options(fn (): array => EducationLevel::query()->forSchool(tenant()?->id ?? '')->pluck('name', 'id')->all()),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                    ])->columns(2),
                Section::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(EntityStatus::class)
                            ->default(EntityStatus::Active)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Programme'),
                Tables\Columns\TextColumn::make('educationLevel.name')
                    ->label('Niveau'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (EntityStatus $state): string => $state->color()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(EntityStatus::class),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStreams::route('/'),
            'create' => Pages\CreateStream::route('/create'),
            'edit' => Pages\EditStream::route('/{record}/edit'),
        ];
    }
}
