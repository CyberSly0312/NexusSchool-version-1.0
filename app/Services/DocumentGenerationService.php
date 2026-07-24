<?php

declare(strict_types=1);

namespace App\Services;

use App\Domains\Document\Enums\DocumentType;
use App\Models\GeneratedDocument;
use App\Models\DocumentTemplate;
use App\Models\Student;

class DocumentGenerationService
{
    public function generateSchoolCertificate(Student $student, array $data = []): GeneratedDocument
    {
        return $this->generate($student, DocumentType::SchoolCertificate, $data);
    }

    public function generateEnrollmentAttestation(Student $student, array $data = []): GeneratedDocument
    {
        return $this->generate($student, DocumentType::EnrollmentAttestation, $data);
    }

    public function generateAttendanceAttestation(Student $student, array $data = []): GeneratedDocument
    {
        return $this->generate($student, DocumentType::AttendanceAttestation, $data);
    }

    public function generateReportCard(Student $student, array $data = []): GeneratedDocument
    {
        return $this->generate($student, DocumentType::ReportCard, $data);
    }

    public function generateTranscript(Student $student, array $data = []): GeneratedDocument
    {
        return $this->generate($student, DocumentType::Transcript, $data);
    }

    public function generateTransferLetter(Student $student, array $data = []): GeneratedDocument
    {
        return $this->generate($student, DocumentType::TransferLetter, $data);
    }

    public function generate(Student $student, DocumentType $type, array $data = []): GeneratedDocument
    {
        $template = DocumentTemplate::where('school_id', $student->school_id)
            ->where('type', $type)
            ->where('is_active', true)
            ->first();

        return GeneratedDocument::create([
            'school_id' => $student->school_id,
            'template_id' => $template?->id,
            'type' => $type,
            'student_id' => $student->id,
            'title' => $type->label() . ' — ' . ($student->student_number ?? $student->id),
            'data' => array_merge($data, [
                'student_number' => $student->student_number ?? '',
                'first_name' => $student->first_name ?? '',
                'last_name' => $student->last_name ?? '',
                'date_of_birth' => $student->date_of_birth?->format('d/m/Y') ?? '',
                'gender' => $student->gender ?? '',
                'generated_date' => now()->format('d/m/Y'),
                'generated_year' => now()->format('Y'),
            ]),
            'generated_by' => auth()->id(),
        ]);
    }
}
