<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">

        <title>Configuration terminée — {{ $school->name }}</title>

        <style>
            :root {
                --ink: #1b1b18;
                --muted: #6f6a63;
                --line: #e6e2d9;
                --bg: #f7f6f2;
                --card: #ffffff;
                --accent: #00a2ff;
            }

            * { box-sizing: border-box; }

            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 24px;
                background: var(--bg);
                color: var(--ink);
                font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            }

            .card {
                width: 100%;
                max-width: 480px;
                padding: 32px;
                border: 1px solid var(--line);
                border-radius: 16px;
                background: var(--card);
                box-shadow: 0 16px 40px rgba(27, 27, 24, 0.08);
                text-align: center;
            }

            h1 { margin: 0 0 12px; font-size: 24px; line-height: 1.25; }
            p { color: var(--muted); line-height: 1.55; }

            a.button {
                display: inline-block;
                margin-top: 20px;
                padding: 12px 20px;
                border-radius: 10px;
                background: var(--accent);
                color: #fff;
                font-weight: 700;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <main class="card">
            <h1>Mot de passe défini</h1>

            @if ($school->isActive())
                <p>
                    Votre compte School Admin est prêt. Vous pouvez maintenant vous
                    connecter au panneau de {{ $school->name }}.
                </p>
                <a class="button" href="{{ route('filament.school-admin.auth.login') }}">
                    Se connecter
                </a>
            @else
                <p>
                    Votre mot de passe est défini. L'école {{ $school->name }} doit encore
                    être activée par la plateforme avant l'ouverture du panneau.
                </p>
            @endif
        </main>
    </body>
</html>
