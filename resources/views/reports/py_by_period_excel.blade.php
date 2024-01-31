<table border="solid" style="font-size:15px;">
    <tr><td colspan="7">{{strtoupper($type_research)}} {{$state}} DEL {{$year}}</td></tr>
    <tr><td colspan="7"><b>Universidad Nacional Agraria de la Selva</b></td></tr>
    <tr><td colspan="7"><b>Unidad de Investigacion de la UNAS</b></td></tr>
    <tr><td colspan="7"><b>{{$school}}</b></td></tr>
    <tr align="center">
        <th>N°</th>
        <th>Codigo</th>
        <th>Nombre de Investigacion</th>
        <th>Responsables</th>
        <th>Areas de Investigacion</th>
        <th>Grupos de Investigacion</th>
        <th>Lineas de Investigacion</th>
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
