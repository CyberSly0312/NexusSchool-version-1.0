<!DOCTYPE html>
<html lang="{{ $template->locale ?? 'fr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->title }}</title>
    <style>
        @page { size: A4; margin: 15mm; }
        @media print { body { margin: 0; } }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: {{ $template->typography['family'] ?? "'Times New Roman', Times, serif" }};
            font-size: {{ $template->typography['size'] ?? '11pt' }};
            line-height: 1.45;
            color: #111;
            background: #fff;
        }

        .document { width: 100%; margin: 0 auto; }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid {{ $template->branding['accent_color'] ?? '#006633' }};
            padding-bottom: 10px;
            margin-bottom: 18px;
        }

        .header .brand { display: flex; align-items: center; gap: 12px; }
        .header .logo { max-height: 56px; max-width: 120px; }
        .header .school-name { font-size: 16pt; font-weight: 700; color: {{ $template->branding['primary_color'] ?? '#006633' }}; }
        .header .school-sub { font-size: 9pt; color: #555; }

        .meta {
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
            color: #444;
            margin-bottom: 14px;
        }

        .title-block { text-align: center; margin-bottom: 18px; }
        .title-block h1 { font-size: 15pt; letter-spacing: 1px; text-transform: uppercase; color: #111; }
        .title-block .period { font-size: 10pt; color: #444; margin-top: 4px; }

        .section { margin-bottom: 16px; }
        .section h2 {
            font-size: 11pt;
            text-transform: uppercase;
            background: {{ $template->branding['section_bg'] ?? '#006633' }};
            color: #fff;
            padding: 4px 8px;
            margin-bottom: 8px;
        }

        table { width: 100%; border-collapse: collapse; font-size: 10pt; }
        th, td { border: 1px solid #bbb; padding: 4px 8px; text-align: left; }
        th { background: #f0f4f1; }

        .student-line { display: flex; justify-content: space-between; font-size: 10.5pt; margin-bottom: 6px; }

        .signatures { display: flex; justify-content: space-between; gap: 20px; margin-top: 28px; }
        .signature { flex: 1; text-align: center; }
        .signature .signature-space { height: 42px; border-bottom: 1px solid #333; margin-bottom: 6px; }
        .signature .role { font-size: 9pt; color: #444; }

        .footer {
            margin-top: 26px;
            border-top: 1px solid #ccc;
            padding-top: 8px;
            font-size: 8.5pt;
            color: #666;
            text-align: center;
        }

        .verification { font-size: 8.5pt; color: #666; margin-top: 8px; text-align: right; }
        .notice { font-size: 9pt; font-style: italic; color: #666; margin-top: 14px; }

        .empty { font-size: 10pt; color: #666; font-style: italic; }
    </style>
</head>
<body>
    <div class="document">
        <div class="header">
            <div class="brand">
                @if (! empty($content['logo_url']))
                    <img class="logo" src="{{ $content['logo_url'] }}" alt="Logo">
                @endif
                <div>
                    <div class="school-name">{{ $content['name'] ?? 'École' }}</div>
                    @if (! empty($content['address']))
                        <div class="school-sub">{{ $content['address'] }}</div>
                    @endif
                    @if (! empty($content['registration_number']))
                        <div class="school-sub">N° d'enregistrement : {{ $content['registration_number'] }}</div>
                    @endif
                </div>
            </div>
            <div class="meta">
                <div>N° {{ $document->document_number }}</div>
                <div>{{ $document->generated_at?->format('d/m/Y') ?? now()->format('d/m/Y') }}</div>
            </div>
        </div>

        <div class="title-block">
            <h1>{{ $document->title }}</h1>
            <div class="period">
                {{ collect([$content['year'] ?? null, $content['term'] ?? null, $content['class'] ?? null, $content['section'] ?? null])->filter()->implode(' — ') }}
            </div>
        </div>

        @if (! empty($content['student']))
            <div class="section">
                <h2>Élève</h2>
                <div class="student-line">
                    <span><strong>{{ $content['student']['full_name'] ?? '' }}</strong></span>
                    <span>{{ $content['student']['class'] ?? '' }} {{ $content['student']['section'] ?? '' }}</span>
                </div>
                @if (! empty($content['student']['student_number']))
                    <div class="student-line"><span>N° matricule</span><span>{{ $content['student']['student_number'] }}</span></div>
                @endif
            </div>
        @endif

        @if (! empty($content['results']))
            <div class="section">
                <h2>Résultats</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th>Moyenne</th>
                            <th>Meilleure note</th>
                            <th>Taux de réussite</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($content['results'] as $result)
                            <tr>
                                <td>{{ $result['subject'] ?? '' }}</td>
                                <td>{{ number_format((float) ($result['average'] ?? 0), 2) }}</td>
                                <td>{{ number_format((float) ($result['best'] ?? ($result['pass_rate'] ? '' : '')), 2) }}</td>
                                <td>{{ number_format((float) ($result['pass_rate'] ?? 0), 1) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if (! empty($content['gradebook']))
            <div class="section">
                <h2>Carnet de notes</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th>Moyenne</th>
                            <th>Coefficient</th>
                            <th>Mention</th>
                            <th>Rang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($content['gradebook'] as $entry)
                            <tr>
                                <td>{{ $entry['subject'] ?? '' }}</td>
                                <td>{{ number_format((float) ($entry['average'] ?? 0), 2) }}</td>
                                <td>{{ $entry['coefficient'] ?? '' }}</td>
                                <td>{{ $entry['appreciation'] ?? $entry['letter_grade'] ?? '' }}</td>
                                <td>{{ $entry['rank'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if (! empty($content['attendance']))
            <div class="section">
                <h2>Présence</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Jours attendus</th>
                            <th>Présences</th>
                            <th>Absences</th>
                            <th>Retards</th>
                            <th>Taux de présence</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $content['attendance']['expected_days'] ?? 0 }}</td>
                            <td>{{ $content['attendance']['present_days'] ?? 0 }}</td>
                            <td>{{ $content['attendance']['absent_days'] ?? 0 }}</td>
                            <td>{{ $content['attendance']['late_days'] ?? 0 }}</td>
                            <td>{{ number_format((float) ($content['attendance']['rate'] ?? 0), 1) }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        @if (! empty($content['decisions']))
            <div class="section">
                <h2>Décisions du conseil</h2>
                @foreach ($content['decisions'] as $decision)
                    <div class="section" style="margin-bottom: 8px;">
                        <strong>{{ $decision['student'] ?? 'Élève' }}</strong> —
                        {{ $decision['summary'] ?? '' }}
                        @if (! empty($decision['recommendation']))
                            <div>Recommandation : {{ $decision['recommendation'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        @if (! empty($content['guardians']))
            <div class="section">
                <h2>Responsables légaux</h2>
                <ul>
                    @foreach ($content['guardians'] as $guardian)
                        <li>{{ $guardian['full_name'] ?? '' }} @if (! empty($guardian['is_primary'])) (responsable principal) @endif</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (! empty($content['summary']))
            <div class="section">
                <h2>Synthèse</h2>
                <table>
                    <tbody>
                        @if (array_key_exists('class_average', $content['summary']))
                            <tr><td>Moyenne de classe</td><td>{{ number_format((float) $content['summary']['class_average'], 2) }}</td></tr>
                        @endif
                        @if (array_key_exists('pass_rate', $content['summary']))
                            <tr><td>Taux de réussite</td><td>{{ number_format((float) $content['summary']['pass_rate'], 1) }}%</td></tr>
                        @endif
                    </tbody>
                </table>
                @if (! empty($content['summary']['entries']))
                    <table style="margin-top: 8px;">
                        <thead>
                            <tr><th>Matière</th><th>Moyenne</th><th>Rang</th><th>Appréciation</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($content['summary']['entries'] as $entry)
                                <tr>
                                    <td>{{ $entry['subject'] ?? '' }}</td>
                                    <td>{{ number_format((float) ($entry['average'] ?? 0), 2) }}</td>
                                    <td>{{ $entry['rank'] ?? '' }}</td>
                                    <td>{{ $entry['appreciation'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endif

        @if (! empty($content['policies']))
            <div class="section">
                <h2>Règlementation applicable</h2>
                <ul>
                    @foreach ($content['policies'] as $policy)
                        <li><strong>{{ $policy['label'] ?? $policy['key'] ?? '' }}</strong> :
                            {{ is_scalar($policy['value'] ?? null) ? $policy['value'] : '' }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (! empty($signatures))
            <div class="signatures">
                @foreach ($signatures as $signature)
                    <div class="signature">
                        <div class="signature-space"></div>
                        <div class="role">{{ $signature->label }}</div>
                    </div>
                @endforeach
            </div>
        @endif

        @if (! empty($content['name']))
            <div class="footer">
                {{ $content['name'] }} — {{ $content['address'] ?? '' }} — {{ $content['email'] ?? '' }} — {{ $content['phone'] ?? '' }}
            </div>
        @endif

        @if ($verificationCodeEnabled)
            <div class="verification">Code de vérification : {{ $document->document_number }}</div>
        @endif
    </div>
</body>
</html>
