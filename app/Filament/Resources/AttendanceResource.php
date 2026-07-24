<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Attendance\Enums\AttendanceStatus;
use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\AttendanceRecord;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Traits\ScopedByTenant;

class AttendanceResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AttendanceRecord::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'Présences';

    protected static ?string $navigationLabel = 'Présences';

    protected static ?string $modelLabel = 'Présence';

    protected static ?string $pluralModelLabel = 'Présences';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Enregistrement de présence')
                ->schema([
                    Forms\Components\DatePicker::make('date')
                        ->label('Date')
                        ->required()
                        ->default(now()->toDateString())
                        ->native(false),
                    Forms\Components\Select::make('student_id')
                        ->label('Élève')
                        ->relationship('student', 'student_number')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options(collect(AttendanceStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->toArray())
                        ->required()
                        ->default(AttendanceStatus::Present->value),
                ])->columns(3),
            Section::make('Détails')
                ->schema([
                    Forms\Components\TimePicker::make('check_in_time')
                        ->label('Heure d\'arrivée'),
                    Forms\Components\TimePicker::make('check_out_time')
                        ->label('Heure de départ'),
                    Forms\Components\Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.student_number')
                    ->label('Élève')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (AttendanceStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('check_in_time')
                    ->label('Arrivée')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('check_out_time')
                    ->label('Départ')
                    ->toggleable(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(collect(AttendanceStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->toArray()),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
