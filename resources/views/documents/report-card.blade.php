<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin Scolaire</title>
    <style>
        @page { size: A4; margin: 15mm; }
        @media print { body { margin: 0; } }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
        }

        .document {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 15mm;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #006633;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .school-info {
            text-align: left;
            flex: 1;
        }

        .school-info p {
            font-size: 9pt;
            margin: 1px 0;
            color: #333;
        }

        .logo-area {
            flex: 0 0 auto;
            text-align: center;
            padding: 0 10px;
        }

        .logo-area img {
            max-height: 60px;
            max-width: 60px;
        }

        .logo-placeholder {
            width: 60px;
            height: 60px;
            border: 1px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7pt;
            color: #999;
            margin: 0 auto;
        }

        .motto {
            font-style: italic;
            font-size: 9pt;
            color: #006633;
            margin-top: 3px;
            letter-spacing: 1px;
        }

        .school-name {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #006633;
            margin: 5px 0;
        }

        .country {
            font-size: 8pt;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .document-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 15px 0 10px;
            color: #000;
            border: 2px solid #006633;
            padding: 8px 15px;
        }

        .trimester-label {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            color: #006633;
            margin-bottom: 10px;
        }

        .reference-date {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 10pt;
        }

        .student-info-bar {
            display: flex;
            justify-content: space-between;
            background: #f0f0f0;
            padding: 8px 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            font-size: 10pt;
        }

        .student-info-bar .info-group {
            margin-right: 15px;
        }

        .student-info-bar .info-label {
            font-weight: bold;
            color: #333;
        }

        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10pt;
        }

        .grades-table th,
        .grades-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            text-align: center;
        }

        .grades-table th {
            background: #006633;
            color: #fff;
            font-weight: bold;
            font-size: 9pt;
        }

        .grades-table .subject-col {
            text-align: left;
            width: 35%;
        }

        .grades-table .coeff-col {
            width: 8%;
        }

        .grades-table .note-col {
            width: 10%;
        }

        .grades-table .total-col {
            width: 12%;
        }

        .grades-table .moy-col {
            width: 10%;
        }

        .grades-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .grades-table .subject-group {
            background: #e8f5e9;
            font-weight: bold;
            text-align: left;
        }

        .grades-table .total-row {
            background: #006633;
            color: #fff;
            font-weight: bold;
        }

        .grades-table .total-row td {
            color: #fff;
            font-weight: bold;
        }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #006633;
            margin: 15px 0 8px;
            border-bottom: 1px solid #006633;
            padding-bottom: 3px;
        }

        .conduct-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10pt;
        }

        .conduct-table th,
        .conduct-table td {
            border: 1px solid #000;
            padding: 5px 8px;
        }

        .conduct-table th {
            background: #006633;
            color: #fff;
            font-weight: bold;
            text-align: left;
            width: 35%;
        }

        .appreciation-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px 0;
            min-height: 60px;
            background: #fafafa;
        }

        .parent-section {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            background: #f9f9f9;
        }

        .parent-section h4 {
            font-size: 11pt;
            color: #006633;
            margin-bottom: 8px;
        }

        .signatures-row {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            width: 30%;
        }

        .signature-box .label {
            font-size: 9pt;
            color: #333;
            margin-bottom: 5px;
        }

        .signature-box .line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 4px;
            font-size: 9pt;
        }

        .stamp-area {
            text-align: center;
            width: 30%;
        }

        .stamp-placeholder {
            width: 100px;
            height: 100px;
            border: 2px dashed #ccc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            color: #999;
            margin: 0 auto;
        }

        .stamp-area img {
            max-width: 100px;
            max-height: 100px;
        }

        .separator {
            border: none;
            border-top: 1px solid #006633;
            margin: 15px 0;
        }

        .center-text {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="document">
        {{-- Header --}}
        <div class="header">
            <div class="header-top">
                <div class="school-info">
                    <div class="school-name">{{ $school->name ?? "Nom de l'école" }}</div>
                    <p>{{ $school->address ?? '' }}{{ isset($school->city) && $school->city ? ' — ' . $school->city : '' }}</p>
                    <p>Tél : {{ $school->phone ?? '' }} | Email : {{ $school->email ?? '' }}</p>
                    @if(!empty($school->registration_number))
                        <p>N° Registre : {{ $school->registration_number }}</p>
                    @endif
                </div>
                <div class="logo-area">
                    @if(!empty($school->logo_url))
                        <img src="{{ $school->logo_url }}" alt="Logo de l'école">
                    @else
                        <div class="logo-placeholder">Logo</div>
                    @endif
                </div>
            </div>
            <p class="country">Burkina Faso</p>
            <p class="motto">« Unité – Progrès – Justice »</p>
        </div>

        {{-- Document Title --}}
        <div class="center-text">
            <div class="document-title">Bulletin Scolaire</div>
            <div class="trimester-label">{{ $data['destination'] ?? '1er Trimestre' }} — Année scolaire {{ $data['generated_year'] ?? date('Y') }} — {{ ($data['generated_year'] ?? date('Y')) + 1 }}</div>
        </div>

        {{-- Reference & Date --}}
        <div class="reference-date">
            <div>
                <strong>Réf. :</strong> {{ $document->reference_number ?? '' }}
            </div>
            <div>
                <strong>Burkina Faso</strong>, le {{ $data['generated_date'] ?? $document->created_at->format('d/m/Y') }}
            </div>
        </div>

        {{-- Student Info Bar --}}
        <div class="student-info-bar">
            <div class="info-group">
                <span class="info-label">Nom & Prénom :</span>
                {{ strtoupper($data['last_name'] ?? '') }} {{ ucfirst($data['first_name'] ?? '') }}
            </div>
            <div class="info-group">
                <span class="info-label">N° Matricule :</span>
                {{ $data['student_number'] ?? '' }}
            </div>
            <div class="info-group">
                <span class="info-label">Né(e) le :</span>
                {{ $data['date_of_birth'] ?? '' }}
            </div>
            <div class="info-group">
                <span class="info-label">Classe :</span>
                {{ $data['destination'] ?? '_______________' }}
            </div>
        </div>

        <hr class="separator">

        {{-- Grades Table --}}
        <table class="grades-table">
            <thead>
                <tr>
                    <th class="subject-col">Matières</th>
                    <th class="coeff-col">Coef.</th>
                    <th class="note-col">Note /20</th>
                    <th class="total-col">Total Pondéré</th>
                    <th class="moy-col">Moy. Élève</th>
                    <th class="moy-col">Moy. Classe</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="subject-group" colspan="6">— Langues et Communication —</td>
                </tr>
                <tr>
                    <td class="subject-col">Français</td>
                    <td class="coeff-col">3</td>
                    <td class="note-col">—</td>
                    <td class="total-col">—</td>
                    <td rowspan="2" class="moy-col">—</td>
                    <td rowspan="2" class="moy-col">—</td>
                </tr>
                <tr>
                    <td class="subject-col">Anglais</td>
                    <td class="coeff-col">2</td>
                    <td class="note-col">—</td>
                    <td class="total-col">—</td>
                </tr>
                <tr>
                    <td class="subject-group" colspan="6">— Mathématiques et Sciences —</td>
                </tr>
                <tr>
                    <td class="subject-col">Mathématiques</td>
                    <td class="coeff-col">4</td>
                    <td class="note-col">—</td>
                    <td class="total-col">—</td>
                    <td rowspan="3" class="moy-col">—</td>
                    <td rowspan="3" class="moy-col">—</td>
                </tr>
                <tr>
                    <td class="subject-col">Physique-Chimie</td>
                    <td class="coeff-col">3</td>
                    <td class="note-col">—</td>
                    <td class="total-col">—</td>
                </tr>
                <tr>
                    <td class="subject-col">SVT</td>
                    <td class="coeff-col">2</td>
                    <td class="note-col">—</td>
                    <td class="total-col">—</td>
                </tr>
                <tr>
                    <td class="subject-group" colspan="6">— Humanités et Société —</td>
                </tr>
                <tr>
                    <td class="subject-col">Histoire-Géographie</td>
                    <td class="coeff-col">2</td>
                    <td class="note-col">—</td>
                    <td class="total-col">—</td>
                    <td rowspan="3" class="moy-col">—</td>
                    <td rowspan="3" class="moy-col">—</td>
                </tr>
                <tr>
                    <td class="subject-col">Éducation Civique</td>
                    <td class="coeff-col">1</td>
                    <td class="note-col">—</td>
                    <td class="total-col">—</td>
                </tr>
                <tr>
                    <td class="subject-col">Franc-Moisin</td>
                    <td class="coeff-col">1</td>
                    <td class="note-col">—</td>
                    <td class="total-col">—</td>
                </tr>
                <tr>
                    <td class="subject-group" colspan="6">— Techniques et Pratiques —</td>
                </tr>
                <tr>
                    <td class="subject-col">Informatique</td>
                    <td class="coeff-col">2</td>
                    <td class="note-col">—</td>
                    <td class="total-col">—</td>
                    <td rowspan="2" class="moy-col">—</td>
                    <td rowspan="2" class="moy-col">—</td>
                </tr>
                <tr>
                    <td class="subject-col">EPS / Sport</td>
                    <td class="coeff-col">1</td>
                    <td class="note-col">—</td>
                    <td class="total-col">—</td>
                </tr>
                <tr class="total-row">
                    <td class="subject-col" style="text-align: right; padding-right: 15px;"><strong>TOTAL</strong></td>
                    <td class="coeff-col"><strong>22</strong></td>
                    <td class="note-col"></td>
                    <td class="total-col"></td>
                    <td class="moy-col"><strong>—/20</strong></td>
                    <td class="moy-col"><strong>—/20</strong></td>
                </tr>
            </tbody>
        </table>

        {{-- Conduct / Appreciation --}}
        <div class="section-title">Appréciation et Comportement</div>
        <table class="conduct-table">
            <tr>
                <th>Discipline</th>
                <td>{{ $data['destination'] ?? '—' }}</td>
            </tr>
            <tr>
                <th>Assiduité</th>
                <td>{{ $data['destination'] ?? '—' }}</td>
            </tr>
            <tr>
                <th>Appréciation générale</th>
                <td>
                    <div class="appreciation-box">
                        {{ $data['destination'] ?? '' }}
                    </div>
                </td>
            </tr>
        </table>

        {{-- Parent Section --}}
        <div class="parent-section">
            <h4>Avis du Parent / Tuteur</h4>
            <div class="appreciation-box" style="min-height: 40px;">
                {{ '' }}
            </div>
        </div>

        {{-- Signatures --}}
        <div class="signatures-row">
            <div class="signature-box">
                <div class="label">Le Directeur(trice)</div>
                <p class="line">{{ $settings['school_director_name'] ?? 'Le Directeur(trice)' }}</p>
            </div>
            <div class="stamp-area">
                @if(!empty($settings['school_stamp_path']))
                    <img src="{{ $settings['school_stamp_path'] }}" alt="Cachet de l'école">
                @else
                    <div class="stamp-placeholder">Cachet de<br>l'école</div>
                @endif
            </div>
            <div class="signature-box">
                <div class="label">Le Parent / Tuteur</div>
                <p class="line">Signature</p>
            </div>
        </div>
    </div>
</body>
</html>
