<table>
    <tr><td colspan="17" align="center" style="background-color: grey;"><b>Universidad Nacional Agraria de la Selva</b>
    </td></tr>
    <tr><td colspan="17"align="center">Unidad de Investigacion de la UNAS</td></tr>
    @foreach($data as $d)
    <tr><td colspan="17" ><b>Registro de proyectos de investigacion {{$d['research_state']}}</b> </td></tr>
    @break
    @endforeach
   <tr align="center">
     <th >N°</th>
     <th >Codigo</th>
     <th >Especialidad</th>
     <th >Nombre de Investigacion</th>
     <th >Obj. General</th>
     <th >Obj. Especificos</th>
     <th >Lineas de Investigacion</th>
     <th >Investigador</th>
     <th >Registro C9</th>
     <th >Recursos Humanos</th>
     <th >Sede o filial</th>
     <th >Fecha Inicio</th>
     <th >Fecha Fin</th>
     <th >Presupuesto</th>
     <th >Entidad financ.</th>
     <th >Recursos Financieros</th>
     <th >Producto/Resultado</th>
  </tr> 
  @foreach($data as $j=>$college)
      <tr><td colspan="17" style="background-color: red;"><b>{{$college['org']}}</b></td></tr>
      @foreach($college['research'] as $r)
    <tr>
      <td>{{$i++}}</td>
      <td>P-{{$j+1}}</td>
      <td>{{$r['college']}}</td>
      <td >{{$r['title']}}</td>
      <td>{{$r['main_objective']['name']}}</td>
      <td>
      @foreach( $r["objectives"] as $objective )
        {{$objective['name'] }}
        <br>
      @endforeach
      </td>
      <td>{{$r['line']}}</td>
      <td> {{$r["main_author"]['name']}}</td>
      <td></td>
      <td>
      @foreach( $r["authors"] as $author )
        {{$author['name'] }} <br>
      @endforeach  
      </td>
      <td>Universidad Nacional Agraria de la Selva</td>
      <td>{{$r['date_init']}}</td>
      <td>{{$r['date_end']}}</td>
      <td>{{$r['budget']}}</td>
      <td>{{$r['fin_company']}}</td>
      <td>{{$r['fin_type']}}</td>
      <td>
      @if(count($r["results"])==1)
        *{{$r["results"][0]  }}.
        @endif
        @if(count($r["results"])>1)
        <br />*{{$r["results"][1] }}
        @endif
        @if(count($r["results"])>2)
        <br />*{{$r["results"][2] }} (Al finalizar el proyecto): {{$r['main_objective']['name']}}
        @endif
        @if(count($r["results"])>3)
        <br />*{{$r["results"][3] }}
        @endif
      </td>


 </tr>
  @endforeach
  @endforeach
  </table>