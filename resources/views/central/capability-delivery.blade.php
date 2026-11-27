<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Lien à usage unique — NexusSchool</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f4f6f8; color: #1f2937; }
        main { max-width: 760px; margin: 4rem auto; padding: 2rem; background: #fff; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,.08); }
        h1 { margin-top: 0; font-size: 1.5rem; }
        input { width: 100%; padding: .75rem; font-size: .9rem; box-sizing: border-box; }
        .notice { padding: .75rem 1rem; border-left: 4px solid #d97706; background: #fffbeb; margin: 1rem 0; }
        .error { color: #b91c1c; }
    </style>
</head>
<body>
<main
    id="capability"
    data-template="{{ $templatePath }}"
    data-domain="{{ $schoolDomain }}"
    data-scheme="{{ $scheme }}"
>
    <h1>Lien à usage unique — {{ $purposeLabel }}</h1>

    <p>
        Établissement concerné : {{ $school->name }}.
        @if ($expiresAt !== null)
            Le lien expire le {{ $expiresAt }}.
        @endif
    </p>

    <div class="notice">
        Ce lien est affiché une seule fois. Il n’a été ni enregistré dans la base de données,
        ni journalisé, ni envoyé par e-mail ou par SMS. Transmettez-le de manière sécurisée
        à la personne concernée. Aucun e-mail ni SMS n’a été envoyé.
    </div>

    <label for="lien">Lien à transmettre</label>
    <input id="lien" type="text" readonly aria-describedby="indication">
    <p id="indication">Le lien apparaît ci-dessous après lecture du jeton contenu dans l’adresse.</p>
    <p id="erreur" class="error" hidden>Le jeton est absent ou invalide. Demandez un nouveau lien à l’opérateur autorisé.</p>
</main>

<script>
    (function () {
        var container = document.getElementById('capability');
        var input = document.getElementById('lien');
        var error = document.getElementById('erreur');
        var fragment = new URLSearchParams(window.location.hash.replace(/^#/, ''));
        var token = fragment.get('jeton') || '';

        // The fragment is client-only: remove it from the address and history
        // before rendering so the capability cannot be copied from the URL bar.
        window.history.replaceState(null, '', window.location.pathname + window.location.search);

        if (!/^[a-f0-9]{64}$/.test(token)) {
            error.hidden = false;
            input.hidden = true;
            return;
        }

        var template = container.getAttribute('data-template');
        var domain = container.getAttribute('data-domain');
        var scheme = container.getAttribute('data-scheme');

        input.value = scheme + '://' + domain + template.replace('__JETON__', token);
        input.focus();
        input.select();
    })();
</script>
</body>
</html>
