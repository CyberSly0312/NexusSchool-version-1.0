<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources;

use App\Domains\Staff\Infrastructure\Models\Certification;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Domains\Staff\Presentation\Filament\Resources\CertificationResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CertificationResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = Certification::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Ressources humaines';

    protected static ?string $navigationLabel = 'Certifications';

    protected static ?string $modelLabel = 'Certification';

    protected static ?string $pluralModelLabel = 'Certifications';

    protected static ?int $navigationSort = 60;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Certification')
                    ->schema([
                        Forms\Components\Select::make('staff_id')->label('Membre du personnel')
                            ->relationship('staff', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn (Staff $record): string => $record->full_name)
                            ->searchable()->preload()->required(),
                        Forms\Components\TextInput::make('name')->label('Intitulé')->required()->maxLength(200),
                        Forms\Components\TextInput::make('issuing_authority')->label('Autorité émettrice')->maxLength(200),
                        Forms\Components\TextInput::make('certification_number')->label('N° de certification')->maxLength(100),
                        Forms\Components\DatePicker::make('issued_at')->label('Délivrée le'),
                        Forms\Components\DatePicker::make('expires_at')->label('Expire le'),
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
                Tables\Columns\TextColumn::make('name')->label('Intitulé')->searchable(),
                Tables\Columns\TextColumn::make('issuing_authority')->label('Autorité')->placeholder('—'),
                Tables\Columns\TextColumn::make('issued_at')->label('Délivrée le')->date(),
                Tables\Columns\TextColumn::make('expires_at')->label('Expire le')
                    ->date()
                    ->color(fn (Certification $record): string => $record->isExpired() ? 'danger' : 'gray'),
            ])
            ->actions([
                EditAction::make()->label('Modifier'),
                DeleteAction::make()->label('Supprimer'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertifications::route('/'),
            'create' => Pages\CreateCertification::route('/create'),
            'edit' => Pages\EditCertification::route('/{record}/edit'),
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
