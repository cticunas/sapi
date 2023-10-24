<!DOCTYPE html>
<html lang="en">
  
<style>
.page-break {
    page-break-after: always;
}
</style>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body style="width:85%">
<div style="font-family:Arial, Helvetica, sans-serif; font: size 15px;">
  <b>
  <p style="text-align: center;" >CONSTANCIA  Nº "NNN" – {{$author['faculty']->code}}.UNAS-T.M.</p>
  <p style="text-transform: uppercase;">EL JEFE DE LA UNIDAD DE INVESTIGACION DE LA {{$author['faculty']->name}} DE LA UNIVERSIDAD  NACIONAL AGRARIA DE LA SELVA, QUE SUSCRIBE,  </p>
<p>HACE CONSTAR:</p> 
  </b>
  
<p style="text-align: justify; line-height:1.5">Que, el {{$author['degree']}} <b>{{$author['name']}} {{$author['lastname']}}</b>, docente {{strtolower($author['condition'])}}, adscrito a la {{$author['faculty']->name}} de esta Casa Superior de Estudios, en calidad de <b>EJECUTOR</b>, se encuentra realizando  los trabajos de investigación siguientes:  </p>

<ol>
  @if( count($research)==0 )
  <h3>0 resultados.</h3>
  @endif
  @foreach($research as $r)
  <li style="font-weight:bold; text-align: justify; padding:10px;">
   {{$r['title']}}.
   </li>
   @endforeach
</ol>

<p> Se expide la presente constancia a solicitud del interesado para los fines y usos a la que hubiere lugar.   </p>
<p style="text-align:right"> Tingo María, {{$now}}</p> 
<br/>
<br/>
<p> Firma</p>
<p>C.c. Archivo.</p>
</div>
</body>
</html>