<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'NexusSchool') }} — {{ $school->name }}</title>

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            :root {
                --portal-ink: #1b1b18;
                --portal-muted: #6f6a63;
                --portal-line: #e6e2d9;
                --portal-bg: #f7f6f2;
                --portal-card: #ffffff;
                --portal-accent: #00a2ff;
            }

            * { box-sizing: border-box; }

            body.portal-shell {
                margin: 0;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                background: var(--portal-bg);
                color: var(--portal-ink);
                font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                line-height: 1.5;
            }

            .portal-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                flex-wrap: wrap;
                padding: 20px 24px;
                background: var(--portal-card);
                border-bottom: 1px solid var(--portal-line);
            }

            .portal-brand {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                font-weight: 600;
                letter-spacing: 0.02em;
            }

            .portal-brand-mark {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 34px;
                height: 34px;
                border-radius: 10px;
                background: #1b1b18;
                color: #ffffff;
                font-size: 16px;
                font-weight: 700;
            }

            .portal-brand-name {
                font-size: 15px;
                text-transform: uppercase;
                letter-spacing: 0.14em;
            }

            .portal-school {
                display: flex;
                align-items: center;
                gap: 12px;
                min-width: 0;
            }

            .portal-logo {
                width: 44px;
                height: 44px;
                border-radius: 12px;
                object-fit: cover;
                border: 1px solid var(--portal-line);
                background: var(--portal-card);
            }

            .portal-logo-fallback {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 44px;
                height: 44px;
                border-radius: 12px;
                border: 1px dashed var(--portal-line);
                background: var(--portal-card);
                color: var(--portal-muted);
                font-weight: 700;
                font-size: 16px;
            }

            .portal-school-copy { min-width: 0; }
            .portal-school-label { margin: 0; font-size: 11px; text-transform: uppercase; letter-spacing: 0.12em; color: var(--portal-muted); }
            .portal-school-name { margin: 0; font-size: 15px; font-weight: 600; overflow-wrap: anywhere; }

            .portal-main {
                width: 100%;
                max-width: 1080px;
                margin: 0 auto;
                padding: 40px 24px 64px;
                display: flex;
                flex-direction: column;
                gap: 32px;
                flex: 1;
            }

            .portal-hero h1 {
                margin: 0 0 8px;
                font-size: clamp(26px, 4vw, 36px);
                line-height: 1.15;
                letter-spacing: -0.01em;
            }

            .portal-hero p {
                margin: 0;
                color: var(--portal-muted);
                font-size: 16px;
                max-width: 56ch;
            }

            .portal-profiles {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 16px;
            }

            .portal-profile-card {
                --portal-accent: var(--portal-accent, #00a2ff);
                display: flex;
                flex-direction: column;
                gap: 8px;
                padding: 20px;
                background: var(--portal-card);
                border: 1px solid var(--portal-line);
                border-top: 3px solid var(--portal-accent);
                border-radius: 14px;
                color: var(--portal-ink);
                text-decoration: none;
                transition: transform 0.12s ease, box-shadow 0.12s ease;
            }

            .portal-profile-card:hover,
            .portal-profile-card:focus-visible {
                transform: translateY(-2px);
                box-shadow: 0 10px 24px rgba(27, 27, 24, 0.08);
            }

            .portal-profile-card:focus-visible {
                outline: 2px solid var(--portal-accent);
                outline-offset: 2px;
            }

            .portal-profile-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border-radius: 10px;
                background: color-mix(in srgb, var(--portal-accent) 12%, #ffffff);
                color: var(--portal-accent);
            }

            .portal-profile-icon svg {
                width: 22px;
                height: 22px;
            }

            .portal-profile-label {
                font-size: 16px;
                font-weight: 700;
            }

            .portal-profile-description {
                font-size: 13px;
                color: var(--portal-muted);
            }

            .portal-note {
                margin: 0;
                font-size: 13px;
                color: var(--portal-muted);
            }

            @media (max-width: 640px) {
                .portal-main { padding: 28px 16px 48px; }
                .portal-header { padding: 16px; }
            }
        </style>
    </head>
    <body class="portal-shell">
        <header class="portal-header">
            <span class="portal-brand">
                <span class="portal-brand-mark">N</span>
                <span class="portal-brand-name">NexusSchool</span>
            </span>

            <div class="portal-school">
                @if (filled($school->logo_url))
                    <img src="{{ $school->logo_url }}" alt="{{ $school->name }} logo" class="portal-logo" />
                @else
                    <div class="portal-logo-fallback" aria-label="{{ $school->name }} logo">{{ \Illuminate\Support\Str::initials($school->name) }}</div>
                @endif
                <div class="portal-school-copy">
                    <p class="portal-school-label">Etablissement</p>
                    <p class="portal-school-name">{{ $school->name }}</p>
                </div>
            </div>
        </header>

        <main class="portal-main">
            <section class="portal-hero">
                <h1>Welcome to {{ $school->name }}</h1>
                <p>Select the profile that matches your role below to continue to the sign-in page.</p>
            </section>

            <section class="portal-profiles" aria-label="Sign-in profiles">
                @foreach ($profiles as $profile)
                    <a href="{{ $profile['login_url'] }}" class="portal-profile-card" style="--portal-accent: {{ $profile['color'] }};">
                        <span class="portal-profile-icon">{!! $profile['icon'] !!}</span>
                        <span class="portal-profile-label">{{ $profile['label'] }}</span>
                        <span class="portal-profile-description">{{ $profile['description'] }}</span>
                    </a>
                @endforeach
            </section>

            <p class="portal-note">After signing in, access is validated against your role and your school. Unauthorized profiles are never granted entry.</p>
        </main>
    </body>
</html>
