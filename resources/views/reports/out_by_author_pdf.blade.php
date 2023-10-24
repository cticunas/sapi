@extends('layouts.pdf')
@section('content')
  </div>
  <table style="width: 100%;">
   <tr align="center">
     <th style="width: 38%;" align="left">Titulo</th>
     <th style="width: 20%;" align="left">Autores</th>
  </tr> 
    @foreach($data as $r)
    <tr>
      <td>{{$r['name']}}</td>
      <td style="font-size: 14px">
      @foreach( $r["authors"] as $author ){{$author['name'] }} <br/>@endforeach  
      </td>
       
 </tr>
  @endforeach
  </table>
@endsection