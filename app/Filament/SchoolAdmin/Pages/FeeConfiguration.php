<?php

declare(strict_types=1);

namespace App\Filament\SchoolAdmin\Pages;

use App\Domains\School\Models\School;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas;
use Filament\Schemas\Schema;

class FeeConfiguration extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Configuration des frais';
    protected static ?string $title = 'Configuration des frais scolaires';
    protected static string|\UnitEnum|null $navigationGroup = 'Finances';
    protected static ?int $navigationSort = 5;
    protected string $view = 'filament.school-admin.pages.fee-configuration';

    public function mount(): void
    {
        $school = School::find(tenant('id'));
        if (! $school) {
            return;
        }

        $settings = $school->settings ?? [];
        $fees = $settings['fees'] ?? [];

        $this->form->fill([
            'payment_cycle' => $fees['payment_cycle'] ?? 'trimester',
            'late_fee_percentage' => $fees['late_fee_percentage'] ?? 0,
            'late_fee_type' => $fees['late_fee_type'] ?? 'percentage',
            'late_fee_fixed_amount' => $fees['late_fee_fixed_amount'] ?? 0,
            'grace_period_days' => $fees['grace_period_days'] ?? 0,
            'enable_auto_late_fee' => $fees['enable_auto_late_fee'] ?? false,
            'enable_partial_payment' => $fees['enable_partial_payment'] ?? true,
            'min_partial_payment_percentage' => $fees['min_partial_payment_percentage'] ?? 25,
            'currency' => $school->currency ?? 'XOF',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Cycle de paiement')
                ->icon('heroicon-o-calendar-days')
                ->schema([
                    Forms\Components\Select::make('payment_cycle')
                        ->label('Cycle de paiement')
                        ->options([
                            'monthly' => 'Mensuel',
                            'trimester' => 'Trimestriel',
                            'semester' => 'Semestriel',
                            'annual' => 'Annuel',
                        ])
                        ->default('trimester')
                        ->required(),
                    Forms\Components\TextInput::make('currency')
                        ->label('Devise')
                        ->default('XOF')
                        ->disabled(),
                ])->columns(2),

            Schemas\Components\Section::make('Frais de retard')
                ->icon('heroicon-o-clock')
                ->schema([
                    Forms\Components\Toggle::make('enable_auto_late_fee')
                        ->label('Activer les frais de retard automatiques')
                        ->default(false),
                    Forms\Components\Select::make('late_fee_type')
                        ->label('Type de frais de retard')
                        ->options([
                            'percentage' => 'Pourcentage (%)',
                            'fixed' => 'Montant fixe',
                        ])
                        ->default('percentage')
                        ->visible(fn (Schemas\Components\Utilities\Get $get) => $get('enable_auto_late_fee')),
                    Forms\Components\TextInput::make('late_fee_percentage')
                        ->label('Taux de frais de retard (%)')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->maxValue(100)
                        ->suffix('%')
                        ->visible(fn (Schemas\Components\Utilities\Get $get) => $get('enable_auto_late_fee') && $get('late_fee_type') === 'percentage'),
                    Forms\Components\TextInput::make('late_fee_fixed_amount')
                        ->label('Montant fixe de frais de retard')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->suffix('FCFA')
                        ->visible(fn (Schemas\Components\Utilities\Get $get) => $get('enable_auto_late_fee') && $get('late_fee_type') === 'fixed'),
                    Forms\Components\TextInput::make('grace_period_days')
                        ->label('Période de grâce (jours)')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->suffix('jours')
                        ->visible(fn (Schemas\Components\Utilities\Get $get) => $get('enable_auto_late_fee')),
                ])->columns(2),

            Schemas\Components\Section::make('Paiements partiels')
                ->icon('heroicon-o-banknotes')
                ->schema([
                    Forms\Components\Toggle::make('enable_partial_payment')
                        ->label('Autoriser les paiements partiels')
                        ->default(true),
                    Forms\Components\TextInput::make('min_partial_payment_percentage')
                        ->label('Pourcentage minimum de paiement partiel')
                        ->numeric()
                        ->default(25)
                        ->minValue(1)
                        ->maxValue(100)
                        ->suffix('%')
                        ->visible(fn (Schemas\Components\Utilities\Get $get) => $get('enable_partial_payment')),
                ])->columns(2),
        ]);
    }

    public function save(): void
    {
        $school = School::find(tenant('id'));
        if (! $school) {
            return;
        }

        $data = $this->form->getState();

        $settings = $school->settings ?? [];
        $settings['fees'] = [
            'payment_cycle' => $data['payment_cycle'],
            'late_fee_percentage' => $data['late_fee_percentage'],
            'late_fee_type' => $data['late_fee_type'],
            'late_fee_fixed_amount' => $data['late_fee_fixed_amount'],
            'grace_period_days' => $data['grace_period_days'],
            'enable_auto_late_fee' => $data['enable_auto_late_fee'],
            'enable_partial_payment' => $data['enable_partial_payment'],
            'min_partial_payment_percentage' => $data['min_partial_payment_percentage'],
        ];

        $school->update([
            'currency' => $data['currency'],
            'settings' => $settings,
        ]);

        Notification::make()
            ->title('Configuration des frais enregistrée')
            ->success()
            ->send();
    }
}
