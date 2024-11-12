<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'FitCheck UF')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href='https://fonts.googleapis.com/css?family=Outfit' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
</head>
<body>
    @include('partials.header')
    <main>
        @yield('content')
    </main>
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
