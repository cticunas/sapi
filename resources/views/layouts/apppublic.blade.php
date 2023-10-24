<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Sistema de Apoyo a la Investigacion</title>
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
  </script>
</head>
<body>
<div id="app">
  <header class="header">
    <nav class="navbar_pub">
      <div class="navbar_title">
        <img class="img_nav" src="{{ asset('images/logoViceInvest.png') }}" alt="Unidad de Gestión de Investigaciónes" height="40">
        <a href="/"><h1 class="main_title">Sistema de Gestión de Investigaciones</h1></a>
      </div>
      <div class="navbar_menu">
        <div class="menu_items">
          <a href="javascript:void(0);" onclick="openModal('documents')" style="display:flex;align-items:center">
            <span class="menu_item_text menu_item_text_docs">Documentos</span>
          </a>
        </div>
        <div class="menu_items">
          <a href="javascript:void(0);" onclick="openModal('tutorials')" style="display:flex;align-items:center">
            <span class="menu_item_text menu_item_text_tutos">Tutoriales</span>
          </a>
        </div>
        <div class="menu_items">
          <a href="/login" style="display:flex;align-items:center">
            <span class="menu_item_text">Iniciar Sesión</span>
            <div class="icon_login"><a-icon type="login" style="color:#fff;font-size:28px;font-weight:800;margin-left:.4em"></a-icon></div>
          </a>
        </div>
      </div>
    </nav>

    <img src="{{ asset('images/vriunas3.png') }}" style="width:100%">

    <p class="navbar navbar-light bg-light">
      / Producción Científica
    </p>
  </header>

  <div class="wrapper" style="height:100%">
    @yield('content')
  </div>

  <footer class="footer" style="position:relative;bottom:0;width:100%;height:60px;">
    <div class="footer_top">
      <p style="margin:0;text-align:center;color:#fff">Email de Soporte: <a href="mailto:sgi.administrador@unas.edu.pe" style="margin-left:.5em;text-decoration:underline;color:#80c2ff;">sgi.administrador@unas.edu.pe</a></p>
    </div>
    <div class="footer_bottom">
      <div style="margin-left:2em;">
        <a href="ocda.unas.edu.pe" target="_blank" style="color:#0f75bc;font-weight:600;">Unidad de Gestión de la Investigación</a>
        <p style="margin: 0">Derechos Reservados © 2021</p>
      </div>
      <div style="margin-right:2em;">
        <a href="www.unas.edu.pe" target="_blank" style="color:#1abb9c;font-weight:600;">Universidad Nacional Agraria de la Selva</a>
        <p style="text-align:end;margin:0">UNAS - Tingo María - Perú</p>
      </div>
    </div>
  </footer>
</div>
<div id="tutorials" class="modal">
  <div class="modal_window">
    <a class="modal_close"  onclick="closeModal('tutorials')" style="color:#fff;font-weight:800;font-size:16px;">X</a>
    <h3>Tutoriales</h3>
    <ul class="unordered_list">
      @foreach($tutos as $tuto)
        <a href="{{ $tuto->url }}" target="_blank" rel="noopener noreferrer">
          <li class="list_item" title="Click para descargar"> {{ $tuto->name }} </li>
         </a>
      @endforeach
      <a href="https://youtu.be/x6SNarjfp_A?t=0" target="_blank" style="margin:.4em 0 0 1.5em;display:flex">
        <li><b style="margin-left:.4em">Ver videotutorial</b></li>
      </a>
    </ul> 
  </div>
</div>
<div id="documents" class="modal">
  <div class="modal_window">
    <a class ="modal_close" onclick="closeModal('documents')" style="color:#fff;font-weight:800;font-size:16px">X</a>
    <h3>Documentos Generales</h3>
    <ul class="unordered_list">
      @foreach($docs as $doc)
        <a href="{{ $doc->url }}" target="_blank" rel="noopener noreferrer">
          <li class="list_item" title="Click para descargar"> {{ $doc->name }} </li>
        </a>
      @endforeach
    </ul> 
  </div>
</div>
</body>
<script>
  document.getElementById('tutorials').style.display = "none";
  document.getElementById('documents').style.display = "none";
  function openModal(type){
    document.getElementById(type).style.display = 'flex';
  }
  function closeModal(type){
    document.getElementById(type).style.display = 'none';
  }
</script>
</html>