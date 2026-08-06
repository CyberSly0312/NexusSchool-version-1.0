<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Presentation\Filament\Resources;

use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Domains\Attendance\Infrastructure\Models\AttendanceRecord;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSession;
use App\Domains\Attendance\Presentation\Filament\Resources\AttendanceRecordResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceRecordResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AttendanceRecord::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Présences';

    protected static ?string $navigationLabel = 'Présences individuelles';

    protected static ?string $modelLabel = 'Présence';

    protected static ?string $pluralModelLabel = 'Présences';

    protected static ?string $recordTitleAttribute = 'uuid';

    protected static ?int $navigationSort = 51;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Séance et élève')
                    ->schema([
                        Forms\Components\Select::make('attendance_session_id')->label('Séance')
                            ->options(fn (): array => AttendanceSession::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->orderByDesc('date')
                                ->limit(200)
                                ->get()
                                ->mapWithKeys(fn ($session) => [$session->id => $session->displayName()])
                                ->all())
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('enrollment_id')
                            ->label('Inscription')
                            ->numeric()
                            ->required(),
                    ])->columns(2),
                Section::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(AttendanceStatus::class)
                            ->default(AttendanceStatus::Present)
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('late_minutes')
                            ->label('Retard (minutes)')
                            ->numeric()
                            ->minValue(0)
                            ->visible(fn (Get $get): bool => $get('status') === AttendanceStatus::Late->value),
                    ])->columns(2),
                Section::make('Horodatages')
                    ->schema([
                        Forms\Components\DateTimePicker::make('check_in_time'),
                        Forms\Components\DateTimePicker::make('check_out_time'),
                        Forms\Components\DateTimePicker::make('recorded_at')
                            ->default(now()),
                        Forms\Components\TextInput::make('notes')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('recorded_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('session.date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('enrollment_id')
                    ->label('Inscription')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (AttendanceStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('late_minutes')
                    ->label('Retard (min)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('recorded_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(AttendanceStatus::class),
                Tables\Filters\SelectFilter::make('attendance_session_id')
                    ->label('Séance')
                    ->options(fn (): array => AttendanceSession::query()
                        ->forSchool(tenant()?->id ?? '')
                        ->orderByDesc('date')
                        ->limit(200)
                        ->get()
                        ->mapWithKeys(fn ($session) => [$session->id => $session->displayName()])
                        ->all()),
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
            'index' => Pages\ListAttendanceRecords::route('/'),
            'create' => Pages\CreateAttendanceRecord::route('/create'),
            'edit' => Pages\EditAttendanceRecord::route('/{record}/edit'),
        ];
    }
}
