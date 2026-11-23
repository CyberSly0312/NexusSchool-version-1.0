<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Access unavailable') }} - {{ $school?->name ?? 'NexusSchool' }}</title>
    <style>
        :root { --bg:#f5f5f2; --card:#fff; --ink:#1b1b18; --muted:#5d6660; --line:#e7ebe8; --danger:#b91c1c; }
        *{box-sizing:border-box} body{margin:0;font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif;background:var(--bg);color:var(--ink);min-height:100vh;display:grid;place-items:center;padding:24px}
        .card{width:100%;max-width:520px;background:var(--card);border:1px solid var(--line);border-radius:16px;padding:28px;box-shadow:0 12px 30px rgba(27,27,24,.05)}
        h1{margin:0 0 8px;font-size:28px} p{margin:0 0 14px;color:var(--muted);line-height:1.5}
        .alert{padding:12px 14px;border-radius:10px;background:#fef2f2;color:var(--danger);border:1px solid #fecaca;margin-bottom:18px}
        button{padding:12px 16px;border:1px solid var(--line);border-radius:10px;background:#fff;color:var(--ink);font:inherit;font-weight:600;cursor:pointer}
    </style>
</head>
<body>
<main class="card">
    <h1>{{ __('Access unavailable') }}</h1>
    <p>{{ $school?->name ?? __('School portal') }}</p>
    <div class="alert" role="alert">{{ __('Your account is authenticated, but no authorized panel is available for this school.') }}</div>
    <p>{{ __('If you believe this is an error, contact your school administrator.') }}</p>
    <form method="POST" action="{{ route('school.gateway.logout') }}">
        @csrf
        <button type="submit">{{ __('Sign out') }}</button>
    </form>
</main>
</body>
</html>
