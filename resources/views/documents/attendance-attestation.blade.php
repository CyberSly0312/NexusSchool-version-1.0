<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attestation de Fréquentation</title>
    <style>
        @page { size: A4; margin: 20mm; }
        @media print { body { margin: 0; } }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
        }

        .document {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 20mm;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #006633;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header-top {
            display: space-between;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            display: flex;
        }

        .school-info {
            text-align: left;
            flex: 1;
        }

        .school-info p {
            font-size: 10pt;
            margin: 2px 0;
            color: #333;
        }

        .logo-area {
            flex: 0 0 auto;
            text-align: center;
            padding: 0 15px;
        }

        .logo-area img {
            max-height: 80px;
            max-width: 80px;
        }

        .logo-placeholder {
            width: 80px;
            height: 80px;
            border: 1px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            color: #999;
            margin: 0 auto;
        }

        .motto {
            font-style: italic;
            font-size: 10pt;
            color: #006633;
            margin-top: 5px;
            letter-spacing: 1px;
        }

        .school-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #006633;
            margin: 8px 0;
        }

        .country {
            font-size: 9pt;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .document-title {
            text-align: center;
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 30px 0 25px;
            color: #000;
            border: 2px solid #006633;
            padding: 10px 20px;
            display: inline-block;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
        }

        .reference-date {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            font-size: 11pt;
        }

        .content {
            margin: 20px 0;
        }

        .content p {
            text-align: justify;
            margin-bottom: 12px;
            font-size: 13pt;
        }

        .student-details {
            margin: 20px 0 20px 20px;
        }

        .student-details table {
            border-collapse: collapse;
            width: 80%;
        }

        .student-details table td {
            padding: 5px 10px;
            font-size: 12pt;
            vertical-align: top;
        }

        .student-details table td:first-child {
            font-weight: bold;
            width: 200px;
        }

        .attendance-summary {
            margin: 25px 0;
            padding: 15px;
            border: 1px solid #ccc;
            background: #f9f9f9;
        }

        .attendance-summary h4 {
            font-size: 12pt;
            color: #006633;
            margin-bottom: 10px;
        }

        .attendance-summary table {
            border-collapse: collapse;
            width: 100%;
            font-size: 11pt;
        }

        .attendance-summary table th,
        .attendance-summary table td {
            border: 1px solid #ccc;
            padding: 6px 10px;
            text-align: center;
        }

        .attendance-summary table th {
            background: #006633;
            color: #fff;
            font-weight: bold;
        }

        .declaration {
            margin-top: 25px;
            text-align: justify;
            font-size: 12pt;
            padding: 15px;
            border: 1px solid #ccc;
            background: #f9f9f9;
        }

        .declaration-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 12pt;
            color: #006633;
        }

        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            page-break-inside: avoid;
        }

        .signature-area {
            text-align: center;
            width: 45%;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 80px;
            padding-top: 5px;
            font-size: 11pt;
        }

        .stamp-area {
            text-align: center;
            width: 25%;
        }

        .stamp-placeholder {
            width: 120px;
            height: 120px;
            border: 2px dashed #ccc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9pt;
            color: #999;
            margin: 0 auto;
        }

        .stamp-area img {
            max-width: 120px;
            max-height: 120px;
        }

        .separator {
            border: none;
            border-top: 1px solid #006633;
            margin: 20px 0;
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
            <div class="document-title">Attestation de Fréquentation Scolaire</div>
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

        <hr class="separator">

        {{-- Content --}}
        <div class="content">
            <p>
                Je soussigné(e), <strong>{{ $settings['school_director_name'] ?? 'Le Directeur' }}</strong>,
                Directeur(trice) de l'établissement <strong>{{ $school->name ?? "Nom de l'école" }}</strong>,
                atteste par la présente que :
            </p>

            <div class="student-details">
                <table>
                    <tr>
                        <td>Nom et Prénom :</td>
                        <td><strong>{{ strtoupper($data['last_name'] ?? '') }} {{ ucfirst($data['first_name'] ?? '') }}</strong></td>
                    </tr>
                    <tr>
                        <td>Né(e) le :</td>
                        <td>{{ $data['date_of_birth'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>Sexe :</td>
                        <td>{{ ($data['gender'] ?? '') === 'M' ? 'Masculin' : 'Féminin' }}</td>
                    </tr>
                    <tr>
                        <td>N° Matricule :</td>
                        <td>{{ $data['student_number'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>Classe :</td>
                        <td>{{ $data['destination'] ?? '_______________' }}</td>
                    </tr>
                </table>
            </div>

            <p>
                fréquente régulièrement les cours de notre établissement durant l'année scolaire
                <strong>{{ $data['generated_year'] ?? date('Y') }} — {{ ($data['generated_year'] ?? date('Y')) + 1 }}</strong>.
            </p>

            <div class="attendance-summary">
                <h4>Récapitulatif de fréquentation</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Période</th>
                            <th>Jours ouvrables</th>
                            <th>Présences</th>
                            <th>Absences</th>
                            <th>Taux de présence</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1er Trimestre</td>
                            <td>—</td>
                            <td>—</td>
                            <td>—</td>
                            <td>—</td>
                        </tr>
                        <tr>
                            <td>2e Trimestre</td>
                            <td>—</td>
                            <td>—</td>
                            <td>—</td>
                            <td>—</td>
                        </tr>
                        <tr>
                            <td>3e Trimestre</td>
                            <td>—</td>
                            <td>—</td>
                            <td>—</td>
                            <td>—</td>
                        </tr>
                        <tr>
                            <td><strong>Total Année</strong></td>
                            <td><strong>—</strong></td>
                            <td><strong>—</strong></td>
                            <td><strong>—</strong></td>
                            <td><strong>—</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="declaration">
                <p class="declaration-title">Observation</p>
                <p>
                    L'élève a fait preuve de régularité et de sérieux dans sa fréquentation scolaire
                    au cours de l'année scolaire considérée. Cette attestation est délivrée pour servir
                    et valoir ce que de droit.
                </p>
            </div>
        </div>

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
                <p class="signature-line">Signature du parent/tuteur</p>
            </div>
        </div>
    </div>
</body>
</html>
