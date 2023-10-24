@extends('layouts.pdf')
@section('content')
  <h4  style="text-transform:uppercase">{{$type_research}} {{$state}} {{$period_type=='T'?'AL TRIMESTRE':'A'}} {{$period}} DEL {{$year}}</h4>
  </div>
  <table style="width: 100%;">
    <th style="width: 70%;" align="left"></th>
    <th style="width: 30%;"></th>
  </table>
  @foreach($data as $org)
    <h4>{{$org["org"]}}</h4>
      @foreach( $org["research"] as $i=> $r )
      <div>
        <p style="margin:5px;">{{$r['code'] }}</p>
        <p  style="margin:5px;">{{$r['title'] }}</p>
        <p  style="margin:5px;">Responsables: </p>
        @foreach($r["authors"] as $a )
        <p  style="margin:5px;"> {{$a["role"]}}: {{$a['name']}}</p>
        @endforeach
      </div>
      <br />
      @endforeach

  @endforeach
@endsection