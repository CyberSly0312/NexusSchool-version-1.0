<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Mot de passe réinitialisé — {{ $school->name }}</title>
</head>
<body>
<main>
    <h1>Mot de passe réinitialisé</h1>
    <p>Votre nouveau mot de passe a été enregistré pour l’établissement {{ $school->name }}.</p>
    <p>Toutes les sessions et tous les liens d’accès précédents ont été révoqués. Connectez-vous à nouveau depuis la passerelle de l’établissement.</p>
    <a href="{{ route('school.gateway.login') }}">Aller à la connexion</a>
</main>
</body>
</html>
