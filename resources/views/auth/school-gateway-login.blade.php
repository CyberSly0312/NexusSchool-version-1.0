<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Sign in') }} - {{ $school?->name ?? 'NexusSchool' }}</title>
    <style>
        :root { --bg:#f5f5f2; --card:#fff; --ink:#1b1b18; --muted:#5d6660; --line:#e7ebe8; --accent:#00a2ff; --danger:#b91c1c; }
        *{box-sizing:border-box} body{margin:0;font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif;background:var(--bg);color:var(--ink);min-height:100vh;display:grid;place-items:center;padding:24px}
        .card{width:100%;max-width:440px;background:var(--card);border:1px solid var(--line);border-radius:16px;padding:28px;box-shadow:0 12px 30px rgba(27,27,24,.05)}
        h1{margin:0 0 6px;font-size:28px;line-height:1.2} p{margin:0 0 20px;color:var(--muted)}
        label{display:block;font-size:14px;font-weight:600;margin-bottom:6px}
        input[type=email],input[type=password]{width:100%;padding:12px 14px;border:1px solid #cfd8d3;border-radius:10px;font:inherit;margin-bottom:14px}
        input:focus{outline:2px solid color-mix(in srgb,var(--accent) 30%,white);border-color:var(--accent)}
        .row{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:4px 0 18px;font-size:14px;color:var(--muted)}
        .row label{margin:0;font-weight:500;display:flex;gap:8px;align-items:center}
        button{width:100%;padding:12px 16px;border:0;border-radius:10px;background:var(--accent);color:#fff;font:inherit;font-weight:700;cursor:pointer}
        button:hover{filter:brightness(.97)}
        .error{background:#fef2f2;color:var(--danger);border:1px solid #fecaca;padding:10px 12px;border-radius:10px;margin-bottom:14px;font-size:14px}
        .school{font-size:13px;color:var(--muted);margin-bottom:18px}
    </style>
</head>
<body>
<main class="card">
    <h1>{{ __('Sign in') }}</h1>
    <p class="school">{{ $school?->name ?? __('School portal') }}</p>
    <p>{{ __('Use your school account credentials. Your authorized panel will be determined after authentication.') }}</p>

    @if ($errors->any())
        <div class="error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('school.gateway.login.submit') }}" novalidate>
        @csrf
        <label for="email">{{ __('Email') }}</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>

        <label for="password">{{ __('Password') }}</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>

        <div class="row">
            <label><input type="checkbox" name="remember" value="1" @checked(old('remember'))> {{ __('Remember me') }}</label>
        </div>

        <button type="submit">{{ __('Sign in') }}</button>
    </form>
</main>
</body>
</html>
