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
    <body style="width:3000px">

        <h4>FORMATO SUNEDU</h4>
        <table border="solid" style="font-size:15px;">
            <tr><td colspan="19" ><b>Universidad Nacional Agraria de la Selva</b></td></tr>
            <tr><td colspan="19" ><b>Unidad de Investigacion de la UNAS</b></td></tr>
            @foreach($data as $d)
                <tr><td colspan="19" ><b>Registro de proyectos de investigacion {{$d['research_state']}}</b> </td></tr>
                @break
            @endforeach
            <tr align="center">
                <th >N°</th>
                <th >Codigo</th>
                <th >Especialidad</th>
                <th style="width:300px" align="left">Nombre de Investigacion</th>
                <th style="width:400px" align="left">Objetivo General</th>
                <th style="width:500px" align="left">Objetivos Especificos</th>
                <th style="width:400px">Areas de Investigacion</th>
                <th style="width:400px">Grupos de Investigacion</th>
                <th style="width:400px">Lineas de Investigacion</th>
                <th >Investigador</th>
                <th >Registro C9</th>
                <th style="width:160px">Recursos Humanos</th>
                <th >Sede o filial</th>
                <th style="width:80px">Fecha Inicio</th>
                <th style="width:80px">Fecha Fin</th>
                <th >Presupuesto</th>
                <th >Entidad financ.</th>
                <th >Recursos Financieros</th>
                <th >Producto/Resultado</th>
            </tr>
            @foreach($data as $j=>$college)
                <tr>
                    <td colspan="19" style="font-size:16px;background-color:#eeeeee "><b>{{$college['org']}}</b></td>
                </tr>
                @foreach($college['research'] as $r)
                    <tr>
                        <td>{{$i++}}</td>
                        <td>P-{{$j+1}}</td>
                        <td>{{$r['college']}}</td>
                        <td style="width: 200px;">{{$r['title']}}</td>
                        <td>{{$r['main_objective']['name']}}</td>
                        <td>
                        @foreach( $r["objectives"] as $objective )
                            {{$objective['name'] }}
                            <br>
                        @endforeach
                        </td>
                        <td>{{$r['area']}}</td>
                        <td>{{$r['group']}}</td>
                        <td>{{$r['line']}}</td>
                        <td> {{$r["main_author"]['name']}}</td>
                        <td></td>
                        <td>
                        @foreach( $r["authors"] as $author )
                            {{$author['name'] }} <br/>
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
                            *{{$r["results"][0] }}.
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
    </body>
</html>
