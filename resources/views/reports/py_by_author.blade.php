@extends('layouts.pdf')
@section('content')
  @foreach($data as $r)
  @if($type_research == 1)
  <h4>RELACION DE TESIS ASESORADAS - NIVEL {{$grade}}</h4>
  @elseif($type_research == 2)
  <h4>RELACION DE INVESTIGACIONES DOCENTE</h4>
  @endif
  <h5>{{ $r['type'] == '1' ? 'Tesista' : 'Investigador' }}: {{$r['name']}} {{$r['lastname']}}  </h5>
  @break
  @endforeach
  </div>
  <table style="width: 100%;">
   <tr align="center">
     <th style="width: 5%;" align="left">N°</th>
     <th style="width: 38%;" align="left">Titulo</th>
     <th style="width: 20%;" align="left">Autores</th>
     <th style="width: 20%;" align="left">Documento</th>
     <th style="width: 10%;" align="left">Estado</th>
     <th style="width: 7%;" align="left">Año</th>
  </tr> 
    @foreach($data as $r)
    <tr>
      <td>{{$i++}}</td>
      <td style="font-size: 13px;">{{$r['title']}}</td>
      <td style="font-size: 13px; padding: 10px;">
      @foreach( $r["authors"] as $author )
        {{$author['name'] }} <br/>
      @endforeach  
      </td>
      <td style="font-size: 13px">{{$r['document']}}</td>
      <td style="font-size: 13px">{{$r['research_state']}}</td>
      <td style="font-size: 13px">{{$r['year_init']}}</td>
       
 </tr>
  @endforeach
  </table>
  
  @endsection