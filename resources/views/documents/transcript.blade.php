<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relevé de Notes</title>
    <style>
        @page { size: A4; margin: 18mm; }
        @media print { body { margin: 0; } }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
        }

        .document {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 18mm;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #006633;
            padding-bottom: 12px;
            margin-bottom: 20px;
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
            max-height: 70px;
            max-width: 70px;
        }

        .logo-placeholder {
            width: 70px;
            height: 70px;
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
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #006633;
            margin: 6px 0;
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
            margin: 20px 0 15px;
            color: #000;
            border: 2px solid #006633;
            padding: 8px 15px;
            display: inline-block;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
        }

        .academic-year {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            color: #006633;
            margin-bottom: 15px;
        }

        .reference-date {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 10pt;
        }

        .student-info {
            margin-bottom: 15px;
            padding: 10px 15px;
            background: #f0f7f0;
            border: 1px solid #006633;
        }

        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .student-info table td {
            padding: 3px 10px;
            font-size: 10.5pt;
        }

        .student-info table td:first-child {
            font-weight: bold;
            width: 160px;
        }

        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10.5pt;
        }

        .grades-table th,
        .grades-table td {
            border: 1px solid #000;
            padding: 6px 10px;
            text-align: center;
        }

        .grades-table th {
            background: #006633;
            color: #fff;
            font-weight: bold;
            font-size: 9.5pt;
        }

        .grades-table .subject-col {
            text-align: left;
            width: 32%;
        }

        .grades-table .coeff-col {
            width: 8%;
        }

        .grades-table .t1-col,
        .grades-table .t2-col,
        .grades-table .t3-col {
            width: 12%;
        }

        .grades-table .avg-col {
            width: 12%;
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

        .summary-section {
            margin: 20px 0;
            page-break-inside: avoid;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
            border: 2px solid #006633;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #000;
            padding: 8px 12px;
        }

        .summary-table th {
            background: #006633;
            color: #fff;
            font-weight: bold;
            text-align: left;
            width: 40%;
        }

        .summary-table td {
            text-align: center;
            font-weight: bold;
        }

        .decision-section {
            margin: 20px 0;
            padding: 15px;
            border: 2px solid #006633;
            background: #f0f7f0;
            text-align: center;
        }

        .decision-section h4 {
            font-size: 12pt;
            color: #006633;
            margin-bottom: 8px;
        }

        .decision-result {
            font-size: 14pt;
            font-weight: bold;
            color: #006633;
        }

        .mention {
            font-size: 12pt;
            font-style: italic;
            color: #555;
            margin-top: 5px;
        }

        .validity-note {
            margin-top: 20px;
            font-size: 10pt;
            font-style: italic;
            color: #555;
            text-align: center;
        }

        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            page-break-inside: avoid;
        }

        .signature-area {
            text-align: center;
            width: 40%;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 70px;
            padding-top: 5px;
            font-size: 10pt;
        }

        .stamp-area {
            text-align: center;
            width: 25%;
        }

        .stamp-placeholder {
            width: 110px;
            height: 110px;
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
            max-width: 110px;
            max-height: 110px;
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
            <div class="document-title">Relevé de Notes Officiel</div>
            <div class="academic-year">Année scolaire {{ $data['generated_year'] ?? date('Y') }} — {{ ($data['generated_year'] ?? date('Y')) + 1 }}</div>
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

        {{-- Student Info --}}
        <div class="student-info">
            <table>
                <tr>
                    <td>Élève :</td>
                    <td><strong>{{ strtoupper($data['last_name'] ?? '') }} {{ ucfirst($data['first_name'] ?? '') }}</strong></td>
                    <td>Né(e) le :</td>
                    <td>{{ $data['date_of_birth'] ?? '' }}</td>
                </tr>
                <tr>
                    <td>Sexe :</td>
                    <td>{{ ($data['gender'] ?? '') === 'M' ? 'Masculin' : 'Féminin' }}</td>
                    <td>N° Matricule :</td>
                    <td>{{ $data['student_number'] ?? '' }}</td>
                </tr>
                <tr>
                    <td>Classe :</td>
                    <td colspan="3"><strong>{{ $data['destination'] ?? '_______________' }}</strong></td>
                </tr>
            </table>
        </div>

        <hr class="separator">

        {{-- Grades Table --}}
        <table class="grades-table">
            <thead>
                <tr>
                    <th class="subject-col">Matières</th>
                    <th class="coeff-col">Coef.</th>
                    <th class="t1-col">1er Trim.</th>
                    <th class="t2-col">2e Trim.</th>
                    <th class="t3-col">3e Trim.</th>
                    <th class="avg-col">Moy. Année</th>
                    <th class="coeff-col">Moy. Pond.</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="subject-group" colspan="7">— Langues et Communication —</td>
                </tr>
                <tr>
                    <td class="subject-col">Français</td>
                    <td class="coeff-col">3</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                </tr>
                <tr>
                    <td class="subject-col">Anglais</td>
                    <td class="coeff-col">2</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                </tr>
                <tr>
                    <td class="subject-group" colspan="7">— Mathématiques et Sciences —</td>
                </tr>
                <tr>
                    <td class="subject-col">Mathématiques</td>
                    <td class="coeff-col">4</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                </tr>
                <tr>
                    <td class="subject-col">Physique-Chimie</td>
                    <td class="coeff-col">3</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                </tr>
                <tr>
                    <td class="subject-col">SVT</td>
                    <td class="coeff-col">2</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                </tr>
                <tr>
                    <td class="subject-group" colspan="7">— Humanités et Société —</td>
                </tr>
                <tr>
                    <td class="subject-col">Histoire-Géographie</td>
                    <td class="coeff-col">2</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                </tr>
                <tr>
                    <td class="subject-col">Éducation Civique</td>
                    <td class="coeff-col">1</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                </tr>
                <tr>
                    <td class="subject-col">Franc-Moisin</td>
                    <td class="coeff-col">1</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                </tr>
                <tr>
                    <td class="subject-group" colspan="7">— Techniques et Pratiques —</td>
                </tr>
                <tr>
                    <td class="subject-col">Informatique</td>
                    <td class="coeff-col">2</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                </tr>
                <tr>
                    <td class="subject-col">EPS / Sport</td>
                    <td class="coeff-col">1</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                </tr>
                <tr class="total-row">
                    <td class="subject-col" style="text-align: right; padding-right: 15px;"><strong>TOTAL</strong></td>
                    <td class="coeff-col"><strong>22</strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><strong>—/20</strong></td>
                </tr>
            </tbody>
        </table>

        {{-- Summary Section --}}
        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    <th>Moyenne Générale Pondérée</th>
                    <td>— / 20</td>
                </tr>
                <tr>
                    <th>Classement / Effectif</th>
                    <td>— / —</td>
                </tr>
                <tr>
                    <th>Mention</th>
                    <td>—</td>
                </tr>
            </table>
        </div>

        {{-- Decision --}}
        <div class="decision-section">
            <h4>DÉCISION DU CONSEIL DE CLASSE</h4>
            <div class="decision-result">ADMIS(E) / AJOURNÉ(E)</div>
            <div class="mention">
                « Ce relevé de notes est un document officiel. Il ne peut être délivré qu'en un seul exemplaire. »
            </div>
        </div>

        <p class="validity-note">
            Présent relevé de notes est délivré pour servir et valoir ce que de droit.
        </p>

        <hr class="separator">

        {{-- Footer --}}
        <div class="footer">
            <div class="signature-area">
                <p class="signature-line">{{ $settings['school_director_name'] ?? 'Le Directeur(trice)' }}</p>
            </div>
            <div class="stamp-area">
                @if(!empty($settings['school_stamp_path']))
                    <img src="{{ $settings['school_stamp_path'] }}" alt="Cachet de l'école">
                @else
                    <div class="stamp-placeholder">Cachet de<br>l'école</div>
                @endif
            </div>
            <div class="signature-area">
                <p class="signature-line">Signature de l'élève</p>
            </div>
        </div>
    </div>
</body>
</html>
