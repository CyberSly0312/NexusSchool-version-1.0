<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Select a panel') }} - {{ $school?->name ?? 'NexusSchool' }}</title>
    <style>
        :root { --bg:#f5f5f2; --card:#fff; --ink:#1b1b18; --muted:#5d6660; --line:#e7ebe8; --accent:#00a2ff; }
        *{box-sizing:border-box} body{margin:0;font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif;background:var(--bg);color:var(--ink);min-height:100vh;display:grid;place-items:center;padding:24px}
        .card{width:100%;max-width:560px;background:var(--card);border:1px solid var(--line);border-radius:16px;padding:28px;box-shadow:0 12px 30px rgba(27,27,24,.05)}
        h1{margin:0 0 6px;font-size:28px} p{margin:0 0 20px;color:var(--muted)}
        .panels{display:grid;gap:12px;margin:20px 0}
        .panel-option{display:flex;gap:12px;align-items:flex-start;padding:14px;border:1px solid var(--line);border-radius:12px}
        .panel-option input{margin-top:4px}
        .panel-label{font-weight:700} .panel-group{font-size:12px;color:var(--muted);text-transform:capitalize}
        button{padding:12px 16px;border:0;border-radius:10px;background:var(--accent);color:#fff;font:inherit;font-weight:700;cursor:pointer}
        .actions{display:flex;gap:12px;align-items:center}
        .logout{background:transparent;color:var(--muted);border:1px solid var(--line)}
    </style>
</head>
<body>
<main class="card">
    <h1>{{ __('Select a panel') }}</h1>
    <p>{{ $school?->name ?? __('School portal') }}</p>
    <p>{{ __('Your account has access to multiple panels. Select one to continue.') }}</p>

    <form method="POST" action="{{ route('school.gateway.enter-panel') }}">
        @csrf
        <div class="panels" role="radiogroup" aria-label="{{ __('Available panels') }}">
            @foreach ($panels as $panel)
                <label class="panel-option">
                    <input type="radio" name="panel" value="{{ $panel['id'] }}" @checked($loop->first) required>
                    <span>
                        <div class="panel-label">{{ $panel['label'] }}</div>
                        @if ($panel['group'])
                            <div class="panel-group">{{ $panel['group'] }}</div>
                        @endif
                    </span>
                </label>
            @endforeach
        </div>
        <div class="actions">
            <button type="submit">{{ __('Continue') }}</button>
        </div>
    </form>

    <form method="POST" action="{{ route('school.gateway.logout') }}" style="margin-top:12px;">
        @csrf
        <button type="submit" class="logout">{{ __('Sign out') }}</button>
    </form>
</main>
</body>
</html>
