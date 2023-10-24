@extends('layouts.pdf')
@section('content')
  <h5> ESCUELA : {{$college->name}}  </h5>
  <table style="width: 100%;">
   <tr align="center">
     <th style="width: 5%;" align="left">N°</th>
     <th style="width: 38%;" align="left">Titulo</th>
     <th style="width: 20%;" align="left">Autores</th>
     <th style="width: 20%;" align="left">Documento</th>
  </tr> 
    @foreach($data as $r)
    <tr>
      <td>{{$i++}}</td>
      <td style="padding:5px 0">{{$r['title']}}.</td>
      <td style="font-size: 14px">
      @foreach( $r["authors"] as $author )  {{$author['name']}} <br />  @endforeach  
      </td>
      <td style="font-size: 14px">{{$r['document']}}</td>
 </tr>
  @endforeach
  </table>
  @endsection