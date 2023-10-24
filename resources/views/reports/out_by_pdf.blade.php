@extends('layouts.pdf')
@section('content')
  <h5> Ejemplares publicados desde: {{$from}} Hasta {{$to}} </h5>
  <table style="width: 100%;">
   <tr align="center">
     <th style="width: 50%;" align="left">Titulo</th>
     <th style="width: 20%;" align="left">Autores</th>
     <th >Tipo</th>
     <th >Indexado</th>
     <th >Fecha</th>
  </tr> 
    @foreach($data as $r)
    <tr>
      <td style="font-size: 14px">{{$r['name']}}</td>
      <td style="font-size: 13px; padding-bottom: 14px;">
      @foreach( $r["authors"] as $author )  {{$author['name']}} <br />  @endforeach  
      </td>
      <td style="text-align: center; font-size: 11px">{{$r['type']}}</td>
      <td style="text-align: center; font-size: 11px">{{$r['indexed']}}</td>
      <td style="text-align: center; font-size: 11px">{{$r['date']}}</td>
 </tr>
  @endforeach
  </table>
  @endsection