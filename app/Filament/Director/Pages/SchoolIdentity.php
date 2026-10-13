<?php

declare(strict_types=1);

namespace App\Filament\Director\Pages;

use App\Domains\School\Models\School;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas;
use Filament\Schemas\Schema;

class SchoolIdentity extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Identité visuelle';
    protected static ?string $title = 'Identité de l\'établissement';
    protected static string|\UnitEnum|null $navigationGroup = 'Paramètres';
    protected static ?int $navigationSort = 100;
    protected string $view = 'filament.director.pages.school-identity';

    public ?string $school_name = null;
    public ?string $school_motto = null;
    public ?string $school_address = null;
    public ?string $school_phone = null;
    public ?string $school_email = null;
    public ?string $school_director_name = null;
    public ?string $school_region = null;
    public ?string $school_department = null;
    public ?string $school_commune = null;

    public function mount(): void
    {
        $school = School::find(tenant('id'));
        if (! $school) {
            return;
        }

        $settings = $school->settings ?? [];
        $this->form->fill([
            'school_name' => $school->name,
            'school_motto' => $settings['school_motto'] ?? 'Unité – Progrès – Justice',
            'school_address' => $school->address,
            'school_phone' => $school->phone,
            'school_email' => $school->email,
            'school_director_name' => $settings['school_director_name'] ?? '',
            'school_region' => $settings['school_region'] ?? '',
            'school_department' => $settings['school_department'] ?? '',
            'school_commune' => $settings['school_commune'] ?? '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Informations générales')
                ->icon('heroicon-o-building-office-2')
                ->schema([
                    Forms\Components\TextInput::make('school_name')
                        ->label('Nom de l\'établissement')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('school_motto')
                        ->label('Devise')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('school_address')
                        ->label('Adresse')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('school_phone')
                        ->label('Téléphone')
                        ->tel()
                        ->maxLength(50),
                    Forms\Components\TextInput::make('school_email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255),
                ])->columns(2),

            Schemas\Components\Section::make('Localisation')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Forms\Components\TextInput::make('school_region')
                        ->label('Région')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('school_department')
                        ->label('Département')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('school_commune')
                        ->label('Commune')
                        ->maxLength(255),
                ])->columns(3),

            Schemas\Components\Section::make('Direction')
                ->icon('heroicon-o-user-circle')
                ->schema([
                    Forms\Components\TextInput::make('school_director_name')
                        ->label('Nom du directeur/directrice')
                        ->maxLength(255),
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
            'name' => $data['school_name'],
            'address' => $data['school_address'],
            'phone' => $data['school_phone'],
            'email' => $data['school_email'],
            'settings' => array_merge($school->settings ?? [], [
                'school_motto' => $data['school_motto'],
                'school_director_name' => $data['school_director_name'],
                'school_region' => $data['school_region'],
                'school_department' => $data['school_department'],
                'school_commune' => $data['school_commune'],
            ]),
        ]);

        Notification::make()
            ->title('Identité de l\'établissement mise à jour')
            ->success()
            ->send();
    }
}
