<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body style="width:2000px">
    <h4 style="text-transform:uppercase">{{$type_research}} {{$state}} DEL {{$year}}</h4>
    <table border="solid" style="font-size:15px;">
        <tr><td colspan="7" ><b>Universidad Nacional Agraria de la Selva</b></td></tr>
        <tr><td colspan="7" ><b>Unidad de Investigacion de la UNAS</b></td></tr>
        <tr><td colspan="7" ><b>{{$school}}</b></td></tr>
        <tr align="center">
            <th>N°</th>
            <th>Codigo</th>
            <th style="width:300px" align="left">Nombre de Investigacion</th>
            <th style="width:400px" align="left">Responsables</th>
            <th style="width:400px">Areas de Investigacion</th>
            <th style="width:400px">Grupos de Investigacion</th>
            <th style="width:400px">Lineas de Investigacion</th>
        </tr>
        @foreach($data as $org)
            @foreach( $org["research"] as $i=> $r )
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $r['code'] }}</td>
                    <td>{{$r['title'] }}</td>
                    <td>
                        @foreach($r["authors"] as $a )
                            {{$a["role"]}}: {{$a['name']}}<br>
                        @endforeach
                    </td>
                    <td>{{$r['area'] }}</td>
                    <td>{{$r['group'] }}</td>
                    <td>{{$r['line'] }}</td>
                </tr>
            @endforeach
        @endforeach
    </table>
</body>
</html>
