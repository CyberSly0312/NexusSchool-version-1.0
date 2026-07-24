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

class PDFSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Paramètres PDF';
    protected static ?string $title = 'Configuration des documents PDF';
    protected static string|\UnitEnum|null $navigationGroup = 'Paramètres';
    protected static ?int $navigationSort = 91;
    protected string $view = 'filament.school-admin.pages.pdf-settings';

    public string $pdf_stamp_path = '';
    public string $pdf_signature_path = '';

    public function mount(): void
    {
        $school = School::find(tenant('id'));
        if (! $school) {
            return;
        }

        $settings = $school->settings ?? [];
        $pdf = $settings['pdf'] ?? [];

        $this->form->fill([
            'pdf_header' => $pdf['header'] ?? '',
            'pdf_footer' => $pdf['footer'] ?? '',
            'pdf_enable_qr_code' => $pdf['enable_qr_code'] ?? false,
            'pdf_enable_page_numbering' => $pdf['enable_page_numbering'] ?? true,
            'pdf_page_format' => $pdf['page_format'] ?? 'A4',
            'pdf_orientation' => $pdf['orientation'] ?? 'portrait',
            'pdf_margin_top' => $pdf['margin_top'] ?? '20',
            'pdf_margin_bottom' => $pdf['margin_bottom'] ?? '20',
            'pdf_margin_left' => $pdf['margin_left'] ?? '15',
            'pdf_margin_right' => $pdf['margin_right'] ?? '15',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('En-tête et pied de page')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\Textarea::make('pdf_header')
                        ->label('En-tête des documents')
                        ->placeholder('Ex: République du Burkina Faso — Ministère de l\'Éducation')
                        ->rows(3),
                    Forms\Components\Textarea::make('pdf_footer')
                        ->label('Pied de page des documents')
                        ->placeholder('Ex: Ce document est officiel et ne peut être falsifié')
                        ->rows(3),
                ])->columns(1),

            Schemas\Components\Section::make('Cachet et signature')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    Forms\Components\FileUpload::make('pdf_stamp_path')
                        ->label('Cachet officiel')
                        ->image()
                        ->imageEditor()
                        ->directory('school-identity/stamps')
                        ->disk('public')
                        ->nullable(),
                    Forms\Components\FileUpload::make('pdf_signature_path')
                        ->label('Signature du directeur')
                        ->image()
                        ->imageEditor()
                        ->directory('school-identity/signatures')
                        ->disk('public')
                        ->nullable(),
                ])->columns(2),

            Schemas\Components\Section::make('Mise en page')
                ->icon('heroicon-o-document-duplicate')
                ->schema([
                    Forms\Components\Select::make('pdf_page_format')
                        ->label('Format de page')
                        ->options([
                            'A4' => 'A4',
                            'A5' => 'A5',
                            'Letter' => 'Lettre',
                        ])
                        ->default('A4'),
                    Forms\Components\Select::make('pdf_orientation')
                        ->label('Orientation')
                        ->options([
                            'portrait' => 'Portrait',
                            'landscape' => 'Paysage',
                        ])
                        ->default('portrait'),
                    Forms\Components\TextInput::make('pdf_margin_top')
                        ->label('Marge supérieure (mm)')
                        ->numeric()
                        ->default(20),
                    Forms\Components\TextInput::make('pdf_margin_bottom')
                        ->label('Marge inférieure (mm)')
                        ->numeric()
                        ->default(20),
                    Forms\Components\TextInput::make('pdf_margin_left')
                        ->label('Marge gauche (mm)')
                        ->numeric()
                        ->default(15),
                    Forms\Components\TextInput::make('pdf_margin_right')
                        ->label('Marge droite (mm)')
                        ->numeric()
                        ->default(15),
                ])->columns(3),

            Schemas\Components\Section::make('Options')
                ->icon('heroicon-o-cog-6-tooth')
                ->schema([
                    Forms\Components\Toggle::make('pdf_enable_page_numbering')
                        ->label('Numérotation des pages')
                        ->default(true),
                    Forms\Components\Toggle::make('pdf_enable_qr_code')
                        ->label('QR Code sur les documents')
                        ->default(false),
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
        $settings['pdf'] = [
            'header' => $data['pdf_header'],
            'footer' => $data['pdf_footer'],
            'enable_qr_code' => $data['pdf_enable_qr_code'],
            'enable_page_numbering' => $data['pdf_enable_page_numbering'],
            'page_format' => $data['pdf_page_format'],
            'orientation' => $data['pdf_orientation'],
            'margin_top' => $data['pdf_margin_top'],
            'margin_bottom' => $data['pdf_margin_bottom'],
            'margin_left' => $data['pdf_margin_left'],
            'margin_right' => $data['pdf_margin_right'],
            'stamp_path' => $data['pdf_stamp_path'] ?? ($settings['pdf']['stamp_path'] ?? null),
            'signature_path' => $data['pdf_signature_path'] ?? ($settings['pdf']['signature_path'] ?? null),
        ];

        $school->update(['settings' => $settings]);

        Notification::make()
            ->title('Paramètres PDF enregistrés')
            ->success()
            ->send();
    }
}
