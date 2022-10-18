<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="app_version" content="{{config('app.version')}}">
    <link rel="icon" type="image/svg" href="/favicon.svg">
    <title inertia>{{ config('app.name', 'Dagobah') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js'])
</head>
<body class="font-sans antialiased">
@inertia

</body>
</html>
