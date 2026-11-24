<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">

        <title>Activation de votre compte — {{ $school->name }}</title>

        <style>
            :root {
                --ink: #1b1b18;
                --muted: #6f6a63;
                --line: #e6e2d9;
                --bg: #f7f6f2;
                --card: #ffffff;
                --accent: #00a2ff;
                --danger: #b42318;
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
            }

            .eyebrow {
                margin: 0 0 8px;
                color: var(--accent);
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            h1 { margin: 0 0 8px; font-size: 24px; line-height: 1.25; }
            p { line-height: 1.55; }
            .muted { color: var(--muted); }

            label {
                display: block;
                margin: 18px 0 6px;
                font-size: 14px;
                font-weight: 600;
            }

            input[type="password"] {
                width: 100%;
                padding: 11px 12px;
                border: 1px solid var(--line);
                border-radius: 10px;
                font-size: 15px;
                background: #fff;
                color: var(--ink);
            }

            input[type="password"]:focus {
                outline: 2px solid rgba(0, 162, 255, 0.35);
                border-color: var(--accent);
            }

            .hint { margin: 8px 0 0; color: var(--muted); font-size: 12px; }
            .error { margin: 8px 0 0; color: var(--danger); font-size: 13px; }

            button {
                width: 100%;
                margin-top: 24px;
                padding: 12px 16px;
                border: 0;
                border-radius: 10px;
                background: var(--accent);
                color: #fff;
                font-size: 15px;
                font-weight: 700;
                cursor: pointer;
            }

            button:hover { filter: brightness(0.95); }

            @media (max-width: 480px) {
                body { padding: 12px; }
                .card { padding: 24px; }
            }
        </style>
    </head>
    <body>
        <main class="card">
            <p class="eyebrow">NexusSchool</p>
            <h1>Activation de votre compte</h1>
            <p class="muted">
                Choisissez votre mot de passe pour activer votre compte. Vous devrez
                ensuite vous connecter normalement.
            </p>

            <form method="POST" action="/account/activate/{{ $token }}" novalidate>
                @csrf

                <label for="password">Nouveau mot de passe</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    required
                    autofocus
                >
                <p class="hint">8 caractères minimum.</p>
                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror

                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    required
                >
                @error('password_confirmation')
                    <p class="error">{{ $message }}</p>
                @enderror

                <button type="submit">Activer mon compte</button>
            </form>
        </main>
    </body>
</html>
