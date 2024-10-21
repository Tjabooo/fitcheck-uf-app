<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'FitCheck UF')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Outfit' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
</head>
<body>
    @include('partials.header')
    <main>
        @yield('content')
    </main>
</body>
</html>
