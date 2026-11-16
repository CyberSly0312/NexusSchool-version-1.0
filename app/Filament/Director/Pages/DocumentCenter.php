<?php

declare(strict_types=1);

namespace App\Filament\Director\Pages;

use App\Domains\Document\Enums\DocumentType;
use App\Models\GeneratedDocument;
use App\Models\Student;
use App\Services\DocumentGenerationService;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class DocumentCenter extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Documents officiels';

    protected static ?string $title = 'Centre documentaire';

    protected static string|\UnitEnum|null $navigationGroup = 'Documents';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.director.pages.document-center';

    public ?string $document_type = null;

    public ?string $student_id = null;

    public ?string $destination = null;

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('document_type')
                ->label('Type de document')
                ->options(collect(DocumentType::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->toArray())
                ->required()
                ->reactive(),
            Forms\Components\Select::make('student_id')
                ->label('Élève')
                ->options(function (): array {
                    $tenantId = tenant()?->getKey();

                    if ($tenantId === null) {
                        return [];
                    }

                    return Student::where('school_id', $tenantId)
                        ->get()
                        ->mapWithKeys(fn ($s) => [$s->id => ($s->student_number ?? $s->id) . ' — ' . ($s->first_name ?? '') . ' ' . ($s->last_name ?? '')])
                        ->toArray();
                })
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\TextInput::make('destination')
                ->label('Destinataire (si applicable)'),
        ])->columns(3);
    }

    public function generateDocument(): void
    {
        $data = $this->form->getState();
        $tenantId = tenant()?->getKey();

        $student = $tenantId === null
            ? null
            : Student::where('school_id', $tenantId)->find($data['student_id']);
        if (! $student) {
            Notification::make()->title('Élève introuvable')->danger()->send();

            return;
        }

        $type = DocumentType::tryFrom($data['document_type']);
        if (! $type) {
            Notification::make()->title('Type de document invalide')->danger()->send();

            return;
        }

        $service = app(DocumentGenerationService::class);
        $document = $service->generate($student, $type, [
            'destination' => $data['destination'] ?? null,
        ]);

        Notification::make()
            ->title('Document généré')
            ->body("Référence: {$document->reference_number}")
            ->success()
            ->send();
    }

    public function getRecentDocuments(): \Illuminate\Support\Collection
    {
        $tenantId = tenant()?->getKey();

        if ($tenantId === null) {
            return collect();
        }

        return GeneratedDocument::where('school_id', $tenantId)
            ->latest()
            ->limit(10)
            ->get();
    }
}
