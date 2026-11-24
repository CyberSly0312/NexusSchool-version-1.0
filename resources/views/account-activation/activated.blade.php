<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">

        <title>Compte activé — {{ $school->name }}</title>

        <style>
            :root {
                --ink: #1b1b18;
                --muted: #6f6a63;
                --line: #e6e2d9;
                --bg: #f7f6f2;
                --card: #ffffff;
                --accent: #00a2ff;
                --success: #067647;
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

            .eyebrow {
                margin: 0 0 8px;
                color: var(--success);
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
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

            @media (max-width: 480px) {
                body { padding: 12px; }
                .card { padding: 24px; }
            }
        </style>
    </head>
    <body>
        <main class="card">
            <p class="eyebrow">NexusSchool</p>
            <h1>Votre compte est activé</h1>
            <p>
                Vous devez maintenant vous connecter avec votre adresse e-mail
                et le mot de passe que vous venez de définir.
            </p>
            <a class="button" href="/login">Se connecter</a>
        </main>
    </body>
</html>
