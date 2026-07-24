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

class SchoolSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Informations de l\'établissement';
    protected static ?string $title = 'Paramètres de l\'établissement';
    protected static string|\UnitEnum|null $navigationGroup = 'Paramètres';
    protected static ?int $navigationSort = 90;
    protected string $view = 'filament.school-admin.pages.school-settings';

    public string $logo_url = '';

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
            'registration_number' => $school->registration_number,
            'motto' => $settings['school_motto'] ?? '',
            'director_name' => $settings['school_director_name'] ?? '',
            'region' => $settings['school_region'] ?? '',
            'department' => $settings['school_department'] ?? '',
            'commune' => $settings['school_commune'] ?? '',
            'currency' => $school->currency ?? 'XOF',
            'locale' => $school->locale ?? 'fr',
            'timezone' => $school->timezone ?? 'Africa/Ouagadougou',
            'logo_url' => $school->logo_url,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Informations générales')
                ->icon('heroicon-o-building-office-2')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom de l\'établissement')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('registration_number')
                        ->label('Numéro d\'enregistrement')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('motto')
                        ->label('Devise de l\'école')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('director_name')
                        ->label('Nom du directeur/directrice')
                        ->maxLength(255),
                ])->columns(2),

            Schemas\Components\Section::make('Coordonnées')
                ->icon('heroicon-o-phone')
                ->schema([
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

            Schemas\Components\Section::make('Localisation')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Forms\Components\TextInput::make('region')
                        ->label('Région')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('department')
                        ->label('Département')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('commune')
                        ->label('Commune')
                        ->maxLength(255),
                ])->columns(3),

            Schemas\Components\Section::make('Paramètres généraux')
                ->icon('heroicon-o-cog-6-tooth')
                ->schema([
                    Forms\Components\Select::make('currency')
                        ->label('Devise')
                        ->options([
                            'XOF' => 'FCFA (Franc CFA)',
                            'EUR' => 'Euro (EUR)',
                            'USD' => 'Dollar US (USD)',
                        ])
                        ->default('XOF')
                        ->required(),
                    Forms\Components\Select::make('locale')
                        ->label('Langue')
                        ->options([
                            'fr' => 'Français',
                            'en' => 'English',
                        ])
                        ->default('fr')
                        ->required(),
                    Forms\Components\Select::make('timezone')
                        ->label('Fuseau horaire')
                        ->options([
                            'Africa/Ouagadougou' => 'Ouagadougou (GMT+0)',
                            'Africa/Abidjan' => 'Abidjan (GMT+0)',
                            'Africa/Dakar' => 'Dakar (GMT+0)',
                            'Africa/Bamako' => 'Bamako (GMT+0)',
                            'Africa/Niamey' => 'Niamey (GMT+1)',
                            'Africa/Brazzaville' => 'Brazzaville (GMT+1)',
                            'Africa/Lagos' => 'Lagos (GMT+1)',
                        ])
                        ->default('Africa/Ouagadougou')
                        ->required(),
                ])->columns(3),

            Schemas\Components\Section::make('Identité visuelle')
                ->icon('heroicon-o-photo')
                ->schema([
                    Forms\Components\FileUpload::make('logo_url')
                        ->label('Logo de l\'établissement')
                        ->image()
                        ->imageEditor()
                        ->directory('school-identity')
                        ->disk('public')
                        ->nullable(),
                ]),
        ]);
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
            'registration_number' => $data['registration_number'],
            'currency' => $data['currency'],
            'locale' => $data['locale'],
            'timezone' => $data['timezone'],
            'logo_url' => $data['logo_url'] ?? $school->logo_url,
            'settings' => array_merge($school->settings ?? [], [
                'school_motto' => $data['motto'],
                'school_director_name' => $data['director_name'],
                'school_region' => $data['region'],
                'school_department' => $data['department'],
                'school_commune' => $data['commune'],
            ]),
        ]);

        Notification::make()
            ->title('Paramètres de l\'établissement enregistrés')
            ->success()
            ->send();
    }
}
