<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Réinitialisation du mot de passe — {{ $school->name }}</title>
    <style>
        :root { --ink: #1b1b18; --muted: #6f6a63; --line: #e6e2d9; --bg: #f7f6f2; --card: #fff; --accent: #00a2ff; --danger: #b42318; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; background: var(--bg); color: var(--ink); font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif; }
        .card { width: 100%; max-width: 520px; background: var(--card); border: 1px solid var(--line); border-radius: 16px; padding: 32px; box-shadow: 0 20px 50px rgba(0,0,0,.06); }
        h1 { margin: 0 0 8px; font-size: 24px; }
        p { color: var(--muted); line-height: 1.6; }
        label { display: block; margin: 18px 0 6px; font-weight: 600; }
        input { width: 100%; padding: 12px 14px; border: 1px solid var(--line); border-radius: 10px; font-size: 15px; }
        button { width: 100%; margin-top: 24px; padding: 13px 16px; border: 0; border-radius: 10px; background: var(--accent); color: #fff; font-size: 16px; font-weight: 700; cursor: pointer; }
        .errors { border-left: 4px solid var(--danger); background: #fff4f2; padding: 12px 16px; border-radius: 8px; color: var(--danger); }
        .errors ul { margin: 6px 0 0; padding-left: 20px; }
        small { display: block; margin-top: 14px; color: var(--muted); }
    </style>
</head>
<body>
<main class="card">
    <h1>Réinitialiser votre mot de passe</h1>
    <p>Établissement : {{ $school->name }}. Définissez vous-même votre nouveau mot de passe. Aucun mot de passe n’a été choisi ni transmis par un autre utilisateur.</p>

    @if ($errors->any())
        <div class="errors" role="alert">
            <strong>Le mot de passe n’a pas été enregistré.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('school.account.password-reset.store', ['token' => $token]) }}">
        @csrf
        <label for="password">Nouveau mot de passe</label>
        <input id="password" name="password" type="password" minlength="12" autocomplete="new-password" required>

        <label for="password_confirmation">Confirmer le nouveau mot de passe</label>
        <input id="password_confirmation" name="password_confirmation" type="password" minlength="12" autocomplete="new-password" required>

        <button type="submit">Enregistrer mon nouveau mot de passe</button>
    </form>

    <small>Ce lien est personnel, valable 24 heures et utilisable une seule fois. Après validation, reconnectez-vous via la passerelle de l’établissement.</small>
</main>
</body>
</html>
