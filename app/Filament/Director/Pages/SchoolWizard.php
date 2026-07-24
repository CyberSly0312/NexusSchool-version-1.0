<?php

declare(strict_types=1);

namespace App\Filament\Director\Pages;

use App\Domains\School\Models\School;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SchoolWizard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Assistant de configuration';
    protected static ?string $title = 'Configuration de l\'établissement';
    protected static string|\UnitEnum|null $navigationGroup = 'Paramètres';
    protected static ?int $navigationSort = 99;
    protected string $view = 'filament.director.pages.school-wizard';

    public int $currentStep = 1;
    public int $totalSteps = 5;

    public ?string $name = null;
    public ?string $address = null;
    public ?string $city = null;
    public ?string $phone = null;
    public ?string $email = null;

    public ?string $current_academic_year = null;
    public ?int $students_per_class = null;
    public ?string $grading_scale = null;

    public ?string $currency = 'XOF';
    public ?string $payment_cycle = null;
    public ?float $late_fee_percentage = null;

    public ?string $motto = null;
    public ?string $director_name = null;
    public ?string $region = null;
    public ?string $department = null;

    public bool $terms_accepted = false;

    public function mount(): void
    {
        $school = School::find(tenant('id'));
        if (! $school) {
            return;
        }

        $settings = $school->settings ?? [];
        $this->form->fill([
            'name' => $school->name,
            'address' => $school->address,
            'city' => $school->city,
            'phone' => $school->phone,
            'email' => $school->email,
            'current_academic_year' => $settings['current_academic_year'] ?? '',
            'students_per_class' => $settings['students_per_class'] ?? 50,
            'grading_scale' => $settings['grading_scale'] ?? '20',
            'currency' => $school->currency ?? 'XOF',
            'payment_cycle' => $settings['payment_cycle'] ?? 'trimester',
            'late_fee_percentage' => $settings['late_fee_percentage'] ?? 0,
            'motto' => $settings['school_motto'] ?? '',
            'director_name' => $settings['school_director_name'] ?? '',
            'region' => $settings['school_region'] ?? '',
            'department' => $settings['school_department'] ?? '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make([
                Forms\Components\Section::make('Informations générales')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom de l\'établissement')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->label('Adresse')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->label('Ville')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                    ])->columns(2),
            ])->visible(fn () => $this->currentStep === 1),

            Forms\Components\Group::make([
                Forms\Components\Section::make('Configuration académique')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Forms\Components\TextInput::make('current_academic_year')
                            ->label('Année scolaire en cours')
                            ->placeholder('2025-2026')
                            ->maxLength(20),
                        Forms\Components\TextInput::make('students_per_class')
                            ->label('Effectif max par classe')
                            ->numeric()
                            ->default(50),
                        Forms\Components\Select::make('grading_scale')
                            ->label('Barème de notation')
                            ->options([
                                '20' => 'Sur 20',
                                '10' => 'Sur 10',
                                '100' => 'Sur 100',
                            ])
                            ->default('20'),
                    ])->columns(3),
            ])->visible(fn () => $this->currentStep === 2),

            Forms\Components\Group::make([
                Forms\Components\Section::make('Configuration financière')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Forms\Components\TextInput::make('currency')
                            ->label('Devise')
                            ->default('XOF')
                            ->maxLength(5),
                        Forms\Components\Select::make('payment_cycle')
                            ->label('Cycle de paiement')
                            ->options([
                                'monthly' => 'Mensuel',
                                'trimester' => 'Trimestriel',
                                'semester' => 'Semestriel',
                                'annual' => 'Annuel',
                            ])
                            ->default('trimester'),
                        Forms\Components\TextInput::make('late_fee_percentage')
                            ->label('Frais de retard (%)')
                            ->numeric()
                            ->default(0),
                    ])->columns(3),
            ])->visible(fn () => $this->currentStep === 3),

            Forms\Components\Group::make([
                Forms\Components\Section::make('Identité visuelle')
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        Forms\Components\TextInput::make('motto')
                            ->label('Devise de l\'école')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('director_name')
                            ->label('Nom du directeur/directrice')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('region')
                            ->label('Région')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('department')
                            ->label('Département')
                            ->maxLength(255),
                    ])->columns(2),
            ])->visible(fn () => $this->currentStep === 4),

            Forms\Components\Group::make([
                Forms\Components\Section::make('Récapitulatif')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Forms\Components\Placeholder::make('review_name')
                            ->label('Nom')
                            ->content(fn () => $this->name ?? '—'),
                        Forms\Components\Placeholder::make('review_address')
                            ->label('Adresse')
                            ->content(fn () => collect([$this->address, $this->city])->filter()->implode(', ') ?: '—'),
                        Forms\Components\Placeholder::make('review_phone')
                            ->label('Téléphone')
                            ->content(fn () => $this->phone ?? '—'),
                        Forms\Components\Placeholder::make('review_academic_year')
                            ->label('Année scolaire')
                            ->content(fn () => $this->current_academic_year ?? '—'),
                        Forms\Components\Placeholder::make('review_grading')
                            ->label('Barème')
                            ->content(fn () => $this->grading_scale ? "Sur {$this->grading_scale}" : '—'),
                        Forms\Components\Placeholder::make('review_payment')
                            ->label('Cycle paiement')
                            ->content(fn () => $this->payment_cycle ?? '—'),
                        Forms\Components\Placeholder::make('review_director')
                            ->label('Directeur/directrice')
                            ->content(fn () => $this->director_name ?? '—'),
                        Forms\Components\Placeholder::make('review_motto')
                            ->label('Devise')
                            ->content(fn () => $this->motto ?? '—'),
                    ])->columns(2),
            ])->visible(fn () => $this->currentStep === 5),
        ]);
    }

    public function nextStep(): void
    {
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function save(): void
    {
        $school = School::find(tenant('id'));
        if (! $school) {
            return;
        }

        $data = $this->form->getState();

        $school->update([
            'name' => $data['name'],
            'address' => $data['address'],
            'city' => $data['city'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'currency' => $data['currency'],
            'settings' => array_merge($school->settings ?? [], [
                'current_academic_year' => $data['current_academic_year'],
                'students_per_class' => $data['students_per_class'],
                'grading_scale' => $data['grading_scale'],
                'payment_cycle' => $data['payment_cycle'],
                'late_fee_percentage' => $data['late_fee_percentage'],
                'school_motto' => $data['motto'],
                'school_director_name' => $data['director_name'],
                'school_region' => $data['region'],
                'school_department' => $data['department'],
            ]),
        ]);

        Notification::make()
            ->title('Configuration de l\'établissement enregistrée avec succès')
            ->success()
            ->send();
    }
}
