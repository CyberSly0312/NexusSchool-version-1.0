<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $school->name }} — NexusSchool</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <style>
            :root {
                --portal-bg: #f5f5f2;
                --portal-surface: #ffffff;
                --portal-ink: #1b1b18;
                --portal-muted: #5d6660;
                --portal-border: #e5e7eb;
                --portal-accent: #00a2ff;
            }

            *, *::before, *::after { box-sizing: border-box; }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                color: var(--portal-ink);
                background:
                    radial-gradient(ellipse 80% 50% at 50% -10%, rgba(0, 162, 255, 0.08), transparent),
                    var(--portal-bg);
            }

            a { color: inherit; text-decoration: none; }

            .portal-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                padding: 20px 24px;
                max-width: 1120px;
                margin: 0 auto;
            }

            .portal-brand {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                font-weight: 700;
                font-size: 18px;
                letter-spacing: -0.01em;
            }

            .portal-brand-mark {
                width: 34px;
                height: 34px;
                border-radius: 10px;
                background: var(--portal-accent);
                color: #fff;
                display: inline-grid;
                place-items: center;
                font-size: 18px;
                font-weight: 800;
            }

            .portal-school {
                display: inline-flex;
                align-items: center;
                gap: 12px;
                font-size: 14px;
                font-weight: 600;
                color: var(--portal-muted);
            }

            .portal-logo,
            .portal-logo-fallback {
                width: 40px;
                height: 40px;
                border-radius: 12px;
                object-fit: cover;
            }

            .portal-logo-fallback {
                display: inline-grid;
                place-items: center;
                background: #e8f6ff;
                color: #0070b8;
                font-weight: 700;
            }

            .portal-main {
                max-width: 1120px;
                margin: 0 auto;
                padding: 48px 24px 72px;
            }

            .portal-hero {
                text-align: center;
                margin-bottom: 40px;
            }

            .portal-hero h1 {
                margin: 0 0 12px;
                font-size: clamp(30px, 5vw, 48px);
                line-height: 1.1;
                letter-spacing: -0.03em;
            }

            .portal-hero p {
                margin: 0;
                color: var(--portal-muted);
                font-size: 17px;
            }

            .portal-actions {
                display: flex;
                justify-content: center;
                margin: 8px 0 20px;
            }

            .portal-login-button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 220px;
                padding: 14px 24px;
                border-radius: 12px;
                background: var(--portal-accent);
                color: #fff;
                font-weight: 700;
                text-decoration: none;
                box-shadow: 0 12px 28px rgba(0, 162, 255, 0.24);
                transition: transform 0.12s ease, box-shadow 0.12s ease;
            }

            .portal-login-button:hover,
            .portal-login-button:focus-visible {
                transform: translateY(-1px);
                box-shadow: 0 16px 32px rgba(0, 162, 255, 0.3);
                outline: none;
            }

            .portal-note {
                text-align: center;
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
                <span>{{ $school->name }}</span>
            </div>
        </header>

        <main class="portal-main">
            <section class="portal-hero">
                <h1>{{ __('Welcome to :school', ['school' => $school->name]) }}</h1>
                <p>{{ __('Sign in with your school account. Your available panels are determined from your assigned roles after authentication.') }}</p>
            </section>

            <div class="portal-actions">
                <a href="{{ route('school.gateway.login') }}" class="portal-login-button">{{ __('Sign in to your school') }}</a>
            </div>

            <p class="portal-note">{{ __('Access is validated against your account, roles and school. Unauthorized panels are never granted entry.') }}</p>
        </main>
    </body>
</html>
