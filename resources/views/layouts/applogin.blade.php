<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- <title>{{ config('app.name', 'Merced Conductiva') }}</title> -->
    <title>Sistema de Apoyo a La Investigacion</title>
    <!-- Scripts -->
    <script>
        var layout = { APP_URL: "{{config('app.url', 'Laravel') }}" } ;
    </script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">

    <script>
        window.USER_ID = "{{ Auth::user()->id ??''}}";
        window.CLEAN_TOKEN = "{{Auth::user()->remember_token??''}}";
		// window._asset = '{{ asset('') }}';
    </script>
</head>
<body>
<div id="app">
    <div  class="wrapper">
        @yield('content')
    </div>
</div>
</body>
</html>
    
