<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Domain\ValueObjects\DocumentNumber;

test('document number formats the canonical identity', function (): void {
    $number = DocumentNumber::fromParts('NS', '2027', 'SCH001', DocumentType::ReportCard, 145);

    expect($number->toString())->toBe('NS-2027-SCH001-RC-000000145');
});

test('document number pads the sequence to nine digits', function (): void {
    $number = DocumentNumber::fromParts('NS', '2027', 'SCH001', DocumentType::CouncilMinutes, 1);

    expect($number->toString())->toBe('NS-2027-SCH001-CM-000000001');
});

test('document number uppercases platform and school code', function (): void {
    $number = DocumentNumber::fromParts('ns', '2027', 'sch001', DocumentType::Certificate, 7);

    expect($number->toString())->toBe('NS-2027-SCH001-CE-000000007');
});

test('document number round-trips through fromString', function (): void {
    $value = 'NS-2027-SCH001-RC-000000145';
    $number = DocumentNumber::fromString($value);

    expect($number->platform)->toBe('NS')
        ->and($number->academicYear)->toBe('2027')
        ->and($number->schoolCode)->toBe('SCH001')
        ->and($number->documentType)->toBe(DocumentType::ReportCard)
        ->and($number->sequence)->toBe(145)
        ->and($number->toString())->toBe($value);
});

test('document number rejects empty parts', function (): void {
    DocumentNumber::fromParts('', '2027', 'SCH001', DocumentType::ReportCard, 1);
})->throws(InvalidArgumentException::class);

test('document number rejects a zero or negative sequence', function (): void {
    DocumentNumber::fromParts('NS', '2027', 'SCH001', DocumentType::ReportCard, 0);
})->throws(InvalidArgumentException::class);

test('document number rejects malformed strings', function (): void {
    DocumentNumber::fromString('NOPE');
})->throws(InvalidArgumentException::class);

test('document number rejects an unknown document type code', function (): void {
    DocumentNumber::fromString('NS-2027-SCH001-XX-000000001');
})->throws(InvalidArgumentException::class);
