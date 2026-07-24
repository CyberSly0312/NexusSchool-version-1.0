<?php

declare(strict_types=1);

namespace App\Filament\SchoolAdmin\Pages;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas;
use Filament\Schemas\Schema;

class ImportData extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationLabel = 'Import de données';
    protected static ?string $title = 'Import Excel / CSV';
    protected static string|\UnitEnum|null $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 30;
    protected string $view = 'filament.school-admin.pages.import-data';

    public string $file = '';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Type d\'import')
                ->icon('heroicon-o-document-arrow-up')
                ->schema([
                    Forms\Components\Select::make('import_type')
                        ->label('Que souhaitez-vous importer ?')
                        ->options([
                            'students' => 'Élèves',
                            'teachers' => 'Enseignants',
                            'guardians' => 'Tuteurs / Parents',
                            'enrollments' => 'Inscriptions',
                            'payments' => 'Paiements',
                        ])
                        ->required()
                        ->reactive(),
                ])->columns(1),

            Schemas\Components\Section::make('Fichier')
                ->icon('heroicon-o-paper-clip')
                ->schema([
                    Forms\Components\FileUpload::make('file')
                        ->label('Fichier à importer')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->maxSize(10240)
                        ->directory('imports')
                        ->disk('local')
                        ->required()
                        ->visible(fn (Schemas\Components\Utilities\Get $get) => filled($get('import_type'))),
                ])->columns(1),

            Schemas\Components\Section::make('Options du fichier')
                ->icon('heroicon-o-cog-6-tooth')
                ->schema([
                    Forms\Components\Toggle::make('has_header')
                        ->label('Le fichier contient une ligne d\'en-tête')
                        ->default(true),
                    Forms\Components\Select::make('delimiter')
                        ->label('Séparateur')
                        ->options([
                            ',' => 'Virgule (,)',
                            ';' => 'Point-virgule (;)',
                            "\t" => 'Tabulation',
                        ])
                        ->default(','),
                    Forms\Components\Select::make('encoding')
                        ->label('Encodage')
                        ->options([
                            'UTF-8' => 'UTF-8',
                            'ISO-8859-1' => 'ISO-8859-1 (Latin)',
                            'Windows-1252' => 'Windows-1252',
                        ])
                        ->default('UTF-8'),
                ])->columns(3)
                ->visible(fn (Schemas\Components\Utilities\Get $get) => filled($get('import_type')) && filled($get('file'))),

            Schemas\Components\Section::make('Prévisualisation')
                ->icon('heroicon-o-eye')
                ->schema([
                    Forms\Components\Placeholder::make('preview_info')
                        ->label('Statut')
                        ->content('La prévisualisation des données sera affichée ici une fois le fichier téléchargé. Le système vérifiera la validité de chaque ligne avant l\'import.'),
                ])
                ->visible(fn (Schemas\Components\Utilities\Get $get) => filled($get('import_type')) && filled($get('file'))),
        ]);
    }

    public function import(): void
    {
        Notification::make()
            ->title('Fonctionnalité d\'import en cours de développement')
            ->warning()
            ->send();
    }
}
