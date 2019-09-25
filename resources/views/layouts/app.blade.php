<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">

    <script src="{{ mix('/js/app.js') }}" type="text/javascript" charset="utf-8"></script>

</head>

<body>
    <div id="app">
        @include('modules.nav')

        <main class="py-4 first-block">
            @yield('content')
        </main>

        @include('modules.footer')

    </div>
</body>
</html>
