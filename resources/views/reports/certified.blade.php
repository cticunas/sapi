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
   <p style="text-align: center;">CERTIFICADO  Nº "NNN" – {{$author['faculty']->code}}.UNAS-T.M.</p>  
  <p style="text-transform: uppercase;text-align:justify" >EL JEFE DE LA UNIDAD DE INVESTIGACION DE LA {{$author['faculty']->name}} DE LA UNIVERSIDAD  NACIONAL AGRARIA DE LA SELVA QUE SUSCRIBE,<b>CERTIFICA:</b></p>

<p style="text-align: justify">Que, el <b>{{$author['degree']}}  <span style="text-transform:uppercase">{{$author['name']}} {{$author['lastname']}}</span></b>, docente de la {{$author['faculty']->name}}  de esta Casa Superior de Estudios 
@if( $role=='Asesor' )
ha participado en calidad de <b>ASESOR DE TESIS {{$grade}}</b> y es parte de la autoría de Artículos Científicos de los siguientes egresados.
@else
en condición de {{strtolower($role)}}, ha desarrollado y cumplido en presentar los Informes Trimestrales e Informes Finales de los trabajos de investigación siguientes:
 @endif
  </p>
  @if( count($research)==0 )
  <h3>0 resultados.</h3>
  @endif
  <ol>
    @if($role=='Asesor')
  @foreach($research as $r)
      <li style="padding:10px">
          <p style="padding:0; margin:0">{{$r['title']}}.</p>
          <table style="width: 100%">
              <tr>
                  <td style="font-weight:bold; font-style:italic; width:80%">
                  @foreach( $r["authors"] as $author )  
                    @if($author['role']=='Titular')
                        <span>Tesista: {{$author['name']}} {{$author['lastname']}} </span>
                    @endif
                    @endforeach  
                  </td>
                  <td style="font-weight:bold; text-align: center; font-style:italic">Año: {{ $r['year'] }}</td>
              </tr>
          </table>
        </li>
     @endforeach
     @else

     @foreach($research as $r)
      <li style="padding:10px">
      @foreach( $r["authors"] as $a )  
            @if($a['id']!=$author['id'])
                <span>{{ strtoupper($a['lastname'])}},  {{$a['name']}}</span>;
            @else
                <span style="font-weight:bold; font-style:italic;">{{ strtoupper($a['lastname'])}},  {{$a['name']}}</span>;
            @endif
        @endforeach  
        <b>{{$r['year'] }}</b>.
        {{$r['title']}}
        </li>
     @endforeach
     @endif
    </ol>
</table>
<br/>

<p style="text-align: justify">Los mencionados trabajos de investigación se encuentran en los archivos de esta Oficina y supervisado por la Unidad de Gestión de Investigación.</p>
<p> Se expide el presente certificado a solicitud del interesado para los fines y usos a la que hubiere lugar.   </p>
<p style="text-align:right"> Tingo María, {{$now}}</p> 
<p> Firma</p>
<p>C.c. Archivo.</p>

</body>
</html>