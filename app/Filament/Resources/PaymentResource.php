<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Payment\Enums\PaymentMethod;
use App\Domains\Payment\Enums\PaymentStatus;
use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use App\Traits\ScopedByTenant;

class PaymentResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = Payment::class;

    protected static ?string $recordTitleAttribute = 'reference';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Finances';

    protected static ?string $navigationLabel = 'Paiements';

    protected static ?string $modelLabel = 'Paiement';

    protected static ?string $pluralModelLabel = 'Paiements';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informations du paiement')
                ->schema([
                    Forms\Components\Select::make('student_id')
                        ->label('Élève')
                        ->relationship('student', 'student_number')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('amount')
                        ->label('Montant')
                        ->numeric()
                        ->prefix('FCFA')
                        ->required(),
                    Forms\Components\Select::make('payment_method')
                        ->label('Mode de paiement')
                        ->options(collect(PaymentMethod::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->toArray())
                        ->required()
                        ->default(PaymentMethod::Cash->value),
                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options(collect(PaymentStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->toArray())
                        ->required()
                        ->default(PaymentStatus::Pending->value),
                ])->columns(2),
            Section::make('Détails')
                ->schema([
                    Forms\Components\TextInput::make('reference')
                        ->label('Référence')
                        ->maxLength(100),
                    Forms\Components\DatePicker::make('due_date')
                        ->label('Échéance')
                        ->native(false),
                    Forms\Components\DatePicker::make('paid_at')
                        ->label('Date de paiement')
                        ->native(false),
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(2),
                    Forms\Components\Textarea::make('notes')
                        ->label('Notes')
                        ->rows(2),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Référence')
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.student_number')
                    ->label('Élève')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Montant')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 0, ',', ' ') . ' FCFA')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Mode')
                    ->formatStateUsing(fn ($state): string => PaymentMethod::tryFrom($state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (PaymentStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Échéance')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Payé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(collect(PaymentStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->toArray()),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
