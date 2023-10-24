@extends('layouts.pdf')
@section('content')
  <h3>Reporte de Incentivos {{$incentive_type=='IC'?' de Iniciación Científica':'FEDU'}} del {{$period_type=='T'?'Trimestre':'Mes'}} {{$period}} - {{$year}} </h3>
  <p>{{"Total:"}}{{$total}}{{" registros."}}</p>
  @foreach($data as $o)
    <h4>{{$o["name"]}}</h4>
      <table style="width:100%">
      @foreach( $o["authors"] as $author )
        <tr>
        <td>{{$i++ }}</td>
          <td>{{$author->author }} {{  strpos($o["name"], "esistas" )>0 ||strpos($o["name"], "studiante" )>0 ?"- DNI: $author->dni":''  }}  </td>
        </tr>
      @endforeach   

      </table>
         
  @endforeach

  @endsection