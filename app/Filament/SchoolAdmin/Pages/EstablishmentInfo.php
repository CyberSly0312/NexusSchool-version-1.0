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

class EstablishmentInfo extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Informations générales';

    protected static string|\UnitEnum|null $navigationGroup = 'Établissement';

    protected static ?int $navigationSort = 11;

    protected string $view = 'filament.school-admin.pages.establishment-info';

    protected static ?string $title = 'Profil de l\'établissement';

    public ?array $data = [];

    public function mount(): void
    {
        $school = School::find(tenant('id'));
        $settings = $school?->settings ?? [];

        $this->form->fill([
            'name' => $school?->name,
            'registration_number' => $school?->registration_number,
            'school_type' => $school?->school_type,
            'address' => $school?->address,
            'city' => $school?->city,
            'state' => $school?->state,
            'country' => $school?->country,
            'phone' => $school?->phone,
            'email' => $school?->email,
            'website' => $settings['website'] ?? '',
            'logo_url' => $school?->logo_url,
            'stamp_path' => $settings['pdf']['pdf_stamp_path'] ?? '',
            'signature_path' => $settings['pdf']['pdf_signature_path'] ?? '',
            'currency' => $school?->currency,
            'locale' => $school?->locale,
            'timezone' => $school?->timezone,
            'date_format' => $settings['date_format'] ?? 'd/m/Y',
            'time_format' => $settings['time_format'] ?? '24h',
            'first_day_of_week' => $settings['first_day_of_week'] ?? 'monday',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Identité de l\'établissement')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom de l\'établissement')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('registration_number')
                            ->label('Code établissement')
                            ->maxLength(100),
                        Forms\Components\Select::make('school_type')
                            ->label('Type d\'établissement')
                            ->options([
                                'primary' => 'Primaire',
                                'secondary' => 'Secondaire',
                                'university' => 'Universitaire',
                                'vocational' => 'Professionnel',
                                'other' => 'Autre',
                            ])
                            ->required(),
                    ])->columns(3),

                Schemas\Components\Section::make('Coordonnées')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Adresse')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->label('Ville')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('state')
                            ->label('Province / Région')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('country')
                            ->label('Pays')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('website')
                            ->label('Site Web')
                            ->url()
                            ->maxLength(255)
                            ->nullable(),
                    ])->columns(3),

                Schemas\Components\Section::make('Identité visuelle')
                    ->schema([
                        Forms\Components\FileUpload::make('logo_url')
                            ->label('Logo')
                            ->image()
                            ->disk('public')
                            ->directory('school-identity')
                            ->nullable(),
                        Forms\Components\FileUpload::make('stamp_path')
                            ->label('Cachet officiel')
                            ->image()
                            ->disk('public')
                            ->directory('school-identity/stamps')
                            ->nullable(),
                        Forms\Components\FileUpload::make('signature_path')
                            ->label('Signature du Directeur')
                            ->image()
                            ->disk('public')
                            ->directory('school-identity/signatures')
                            ->nullable(),
                    ])->columns(3),

                Schemas\Components\Section::make('Paramètres régionaux')
                    ->schema([
                        Forms\Components\Select::make('currency')
                            ->label('Devise')
                            ->options([
                                'XOF' => 'XOF (FCFA CFA)',
                                'EUR' => 'EUR (Euro)',
                                'USD' => 'USD (Dollar)',
                            ])
                            ->required(),
                        Forms\Components\Select::make('locale')
                            ->label('Langue')
                            ->options([
                                'fr' => 'Français',
                                'en' => 'English',
                            ])
                            ->required(),
                        Forms\Components\Select::make('timezone')
                            ->label('Fuseau horaire')
                            ->options([
                                'Africa/Abidjan' => 'Abidjan (GMT+0)',
                                'Africa/Accra' => 'Accra (GMT+0)',
                                'Africa/Lagos' => 'Lagos (GMT+1)',
                                'Africa/Kinshasa' => 'Kinshasa (GMT+1)',
                                'Africa/Porto-Novo' => 'Porto-Novo (GMT+1)',
                                'Africa/Douala' => 'Douala (GMT+1)',
                                'Africa/Ndjamena' => 'N\'Djamena (GMT+1)',
                                'Africa/Brazzaville' => 'Brazzaville (GMT+1)',
                                'Africa/Luanda' => 'Luanda (GMT+1)',
                                'UTC' => 'UTC',
                            ])
                            ->searchable()
                            ->required(),
                    ])->columns(3),

                Schemas\Components\Section::make('Format et horaires')
                    ->schema([
                        Forms\Components\Select::make('date_format')
                            ->label('Format de la date')
                            ->options([
                                'd/m/Y' => 'DD/MM/YYYY',
                                'm/d/Y' => 'MM/DD/YYYY',
                                'Y-m-d' => 'YYYY-MM-DD',
                            ])
                            ->required(),
                        Forms\Components\Select::make('time_format')
                            ->label('Format de l\'heure')
                            ->options([
                                '24h' => '24 heures',
                                '12h' => '12 heures (AM/PM)',
                            ])
                            ->required(),
                        Forms\Components\Select::make('first_day_of_week')
                            ->label('Premier jour de semaine')
                            ->options([
                                'monday' => 'Lundi',
                                'sunday' => 'Dimanche',
                            ])
                            ->required(),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $school = School::find(tenant('id'));

        if (! $school) {
            Notification::make()->danger()->title('École introuvable')->send();
            return;
        }

        $directFields = [
            'name', 'registration_number', 'school_type', 'address',
            'city', 'state', 'country', 'phone', 'email', 'currency', 'locale', 'timezone',
        ];

        foreach ($directFields as $field) {
            $school->{$field} = $data[$field] ?? $school->{$field};
        }

        $school->logo_url = $data['logo_url'] ?? $school->logo_url;

        $settings = $school->settings ?? [];
        $settings['website'] = $data['website'] ?? '';
        $settings['date_format'] = $data['date_format'] ?? 'd/m/Y';
        $settings['time_format'] = $data['time_format'] ?? '24h';
        $settings['first_day_of_week'] = $data['first_day_of_week'] ?? 'monday';

        $settings['pdf'] = $settings['pdf'] ?? [];
        $settings['pdf']['pdf_stamp_path'] = $data['stamp_path'] ?? '';
        $settings['pdf']['pdf_signature_path'] = $data['signature_path'] ?? '';

        $school->settings = $settings;
        $school->save();

        Notification::make()->success()->title('Profil de l\'établissement mis à jour')->send();
    }
}
