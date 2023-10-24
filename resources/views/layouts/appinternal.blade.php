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
<div id="app" style='display:none;'>
    <header>
        <div class="row justify-content-center" style="margin-right:0; margin-left:0;">
            <div class="col-md-12" style="padding-right:0; padding-left:0;">
                <nav class="navbar navbar-light bg-light">
                    <div style="width:100%;display:flex;align-items:center;justify-content:space-between;">
                        <div class="d-flex">
                            <div style="display:flex;align-items:center;margin-right: 8px; height: 40px">
                                <button type="button" id="sidebarCollapse" class="btn btn-outline-secondary btn-sm">
                                    <span class="navbar-toggler-icon"></span>
                                </button>
                            </div>
                            <div class="img-logo">
                                <a class="navbar-brand" href="{{ url('/research') }}" style="display:flex;align-items:center;padding:0; margin:0">
                                    <div>
                                        <img class="logo img-fluid" src="{{asset('images/logounas.png')}}" style="width:40px" />
                                    </div>
                                    <div style="font-size:14px;white-space: normal;line-height:1.2">
                                        Sistema de Apoyo a la Investigacion
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div>
                            <p style="margin:0;;font-size:12px;color:#999;text-align: right  " > Soporte:  <br /> <span style="color:#333">sgi.administrador@unas.edu.pe</span> </p>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    <div  class="wrapper">
        <nav id="sidebar">
        @guest @else
            <ul class="list-unstyled components" style="padding:0;margin:0">
                <li class="nav-item dropdown" style="padding:10px 0;">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="{{Auth::user()->role->name}}" v-pre>
                        <img class="user_avatar" src="{!! Auth::user()->photo() !!}" /> {{ Auth::user()->username }}
                        <span class="caret"></span>
                    </a>
        
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="padding-left:10px">{{__('Cerrar Sesion') }} </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
            @endguest
            <menu-component base-url="{{url('/')}}" current-url="{{Request::url()}}" user="{{Auth::user()}}"></menu-component>
        </nav>
        <div id="content" style="width:100%; padding:10px;">
            <router-view :key="$route.fullPath"> @yield('content') </router-view>
        </div>
    </div>
</div>
</body>
</html>
    
