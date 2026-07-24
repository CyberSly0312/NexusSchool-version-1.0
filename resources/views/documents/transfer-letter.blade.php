<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lettre de Transfert</title>
    <style>
        @page { size: A4; margin: 20mm; }
        @media print { body { margin: 0; } }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13pt;
            line-height: 1.6;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
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

        .letter-from {
            margin-bottom: 25px;
            padding: 10px;
            border-left: 3px solid #006633;
        }

        .letter-from p {
            margin: 3px 0;
            font-size: 11pt;
        }

        .letter-to {
            margin-bottom: 20px;
            font-size: 12pt;
        }

        .letter-to p {
            margin: 3px 0;
        }

        .subject-line {
            margin: 20px 0;
            font-weight: bold;
            font-size: 13pt;
            text-decoration: underline;
            color: #006633;
        }

        .content {
            margin: 20px 0;
        }

        .content p {
            text-align: justify;
            margin-bottom: 15px;
            font-size: 13pt;
        }

        .student-details {
            margin: 20px 0 20px 20px;
            padding: 15px;
            background: #f0f7f0;
            border: 1px solid #006633;
        }

        .student-details table {
            border-collapse: collapse;
            width: 100%;
        }

        .student-details table td {
            padding: 5px 10px;
            font-size: 12pt;
            vertical-align: top;
        }

        .student-details table td:first-child {
            font-weight: bold;
            width: 220px;
        }

        .conduct-confirmation {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ccc;
            background: #f9f9f9;
            text-align: center;
        }

        .conduct-confirmation h4 {
            color: #006633;
            margin-bottom: 8px;
            font-size: 12pt;
        }

        .conduct-confirmation p {
            font-style: italic;
            font-size: 12pt;
        }

        .reason-section {
            margin: 15px 0;
        }

        .reason-section h4 {
            font-size: 12pt;
            color: #006633;
            margin-bottom: 8px;
        }

        .reason-box {
            border: 1px solid #ccc;
            padding: 10px;
            min-height: 60px;
            background: #fafafa;
            font-style: italic;
        }

        .closing {
            margin-top: 25px;
            font-size: 13pt;
        }

        .closing p {
            margin-bottom: 10px;
        }

        .signature-area {
            text-align: right;
            margin-top: 50px;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 80px;
            padding-top: 5px;
            font-size: 11pt;
            display: inline-block;
            width: 250px;
        }

        .stamp-area {
            text-align: center;
            margin-top: 20px;
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

        .footer-note {
            margin-top: 30px;
            font-size: 9pt;
            color: #777;
            text-align: center;
            font-style: italic;
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
            <div class="document-title">Lettre de Transfert</div>
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

        {{-- From School --}}
        <div class="letter-from">
            <p><strong>Expéditeur :</strong></p>
            <p>{{ $school->name ?? "Nom de l'école" }}</p>
            <p>{{ $school->address ?? '' }}{{ isset($school->city) && $school->city ? ', ' . $school->city : '' }}</p>
            <p>Tél : {{ $school->phone ?? '' }}</p>
        </div>

        {{-- To School --}}
        <div class="letter-to">
            <p><strong>Destinataire :</strong></p>
            <p>À Monsieur/Madame le Directeur(trice)</p>
            <p>de l'établissement <strong>{{ $data['destination'] ?? '____________________' }}</strong></p>
            <p>À <strong>____________________</strong></p>
        </div>

        {{-- Subject --}}
        <div class="subject-line">
            Objet : Transfert scolaire
        </div>

        {{-- Content --}}
        <div class="content">
            <p>
                Monsieur/Madame le Directeur(trice),
            </p>

            <p>
                J'ai l'honneur de vous adresser la présente lettre de transfert concernant l'élève
                ci-dessous nommé(e), qui quitte notre établissement pour des raisons familiales.
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
                        <td>Dernière classe fréquentée :</td>
                        <td><strong>{{ $data['destination'] ?? '_______________' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Année scolaire :</td>
                        <td>{{ $data['generated_year'] ?? date('Y') }} — {{ ($data['generated_year'] ?? date('Y')) + 1 }}</td>
                    </tr>
                </table>
            </div>

            <p>
                Le transfert de cet(tte) élève est motivé par les raisons suivantes :
            </p>

            <div class="reason-section">
                <div class="reason-box">
                    {{ $data['destination'] ?? 'Raisons familiales / Mutation / Changement de résidence' }}
                </div>
            </div>

            <div class="conduct-confirmation">
                <h4>Attestation de Bonne Conduite</h4>
                <p>
                    Je certifie que cet(te) élève a fait preuve d'une bonne conduite et d'un comportement
                    irréprochable durant toute la période de sa scolarité dans notre établissement.
                    Il/Elle a toujours fait preuve de sérieux et de respect envers le corps enseignant
                    et le personnel administratif.
                </p>
            </div>

            <p>
                Je vous prie de bien vouloir accueillir cet(te) élève dans votre établissement et de
                lui faciliter les formalités d'inscription nécessaires.
            </p>

            <p>
                Je reste à votre entière disposition pour tout renseignement complémentaire que
                vous jugerez utile.
            </p>

            <div class="closing">
                <p>Veuillez agréer, Monsieur/Madame le Directeur(trice), l'expression de mes salutations
                distinguées.</p>
            </div>
        </div>

        <hr class="separator">

        {{-- Signature --}}
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

        <p class="footer-note">
            La présente lettre de transfert est valable pour une durée de trois (3) mois à compter de sa date de délivrance.
        </p>
    </div>
</body>
</html>
