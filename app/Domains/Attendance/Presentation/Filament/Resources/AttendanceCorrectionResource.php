<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Presentation\Filament\Resources;

use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Domains\Attendance\Domain\ValueObjects\CorrectionReason;
use App\Domains\Attendance\Infrastructure\Models\AttendanceCorrection;
use App\Domains\Attendance\Infrastructure\Models\AttendanceRecord;
use App\Domains\Attendance\Presentation\Filament\Resources\AttendanceCorrectionResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceCorrectionResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AttendanceCorrection::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static string|\UnitEnum|null $navigationGroup = 'Présences';

    protected static ?string $navigationLabel = 'Corrections';

    protected static ?string $modelLabel = 'Correction';

    protected static ?string $pluralModelLabel = 'Corrections';

    protected static ?string $recordTitleAttribute = 'uuid';

    protected static ?int $navigationSort = 52;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Correction')
                    ->schema([
                        Forms\Components\Select::make('attendance_record_id')->label('Présence')
                            ->options(fn (): array => AttendanceRecord::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->orderByDesc('recorded_at')
                                ->limit(200)
                                ->get()
                                ->mapWithKeys(fn ($record) => [$record->id => sprintf('#%s — %s', $record->id, $record->status->label())])
                                ->all())
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('previous_status')->label('Statut précédent')
                            ->options(AttendanceStatus::class)
                            ->required(),
                        Forms\Components\Select::make('new_status')->label('Nouveau statut')
                            ->options(AttendanceStatus::class)
                            ->required(),
                    ])->columns(3),
                Section::make('Justification')
                    ->schema([
                        Forms\Components\Select::make('reason_type')->label('Motif')
                            ->options([
                                CorrectionReason::TYPE_ERROR => 'Erreur de saisie',
                                CorrectionReason::TYPE_EXCUSE_APPROVED => 'Excuse approuvée',
                                CorrectionReason::TYPE_MEDICAL_CERTIFICATE => 'Certificat médical',
                                CorrectionReason::TYPE_SCHOOL_ACTIVITY => 'Activité scolaire',
                                CorrectionReason::TYPE_ADMINISTRATIVE => 'Administratif',
                                CorrectionReason::TYPE_OTHER => 'Autre',
                            ])
                            ->default(CorrectionReason::TYPE_OTHER)
                            ->required(),
                        Forms\Components\Textarea::make('reason_text')
                            ->label('Détails')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(1),
                Section::make('Autorisation')
                    ->schema([
                        Forms\Components\Toggle::make('is_authorized')
                            ->label('Autorisée')
                            ->default(true)
                            ->required(),
                        Forms\Components\DateTimePicker::make('corrected_at')
                            ->default(now())
                            ->required(),
                        Forms\Components\TextInput::make('notes')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('corrected_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('record.id')
                    ->label('Présence')
                    ->sortable(),
                Tables\Columns\TextColumn::make('previous_status')
                    ->label('Avant')
                    ->badge()
                    ->color(fn (AttendanceStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('new_status')
                    ->label('Après')
                    ->badge()
                    ->color(fn (AttendanceStatus $state): string => $state->color()),
                Tables\Columns\IconColumn::make('is_authorized')
                    ->label('Autorisée')
                    ->boolean(),
                Tables\Columns\TextColumn::make('reason_text')
                    ->label('Motif')
                    ->limit(30),
                Tables\Columns\TextColumn::make('corrected_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceCorrections::route('/'),
            'create' => Pages\CreateAttendanceCorrection::route('/create'),
            'edit' => Pages\EditAttendanceCorrection::route('/{record}/edit'),
        ];
    }
}
