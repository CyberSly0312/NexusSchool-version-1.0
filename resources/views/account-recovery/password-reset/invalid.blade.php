<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Lien invalide — {{ $school->name }}</title>
</head>
<body>
<main>
    <h1>Lien de réinitialisation indisponible</h1>
    <p>Ce lien est invalide, expiré, déjà utilisé ou révoqué. Aucun mot de passe n’a été modifié.</p>
    <p>Contactez un Super Admin pour demander un nouveau lien de réinitialisation.</p>
</main>
</body>
</html>
