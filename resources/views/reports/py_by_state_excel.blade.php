<table border="solid" style="font-size:15px;">
    <tr><td colspan="7"><b>Universidad Nacional Agraria de la Selva</b></td></tr>
    <tr><td colspan="7"><b>Unidad de Investigacion de la UNAS</b></td></tr>

    <tr>
        <td colspan="7">
            <b>
                @foreach($data as $r)
                    ESTADO : {{strtoupper($r['research_state'])}} @break
                @endforeach
            </b>
        </td>
    </tr>
    <tr align="center">
        <th>N°</th>
        <th>Titulo</th>
        <th>Autores</th>
        <th>Documento</th>
        <th>Areas de Investigacion</th>
        <th>Grupos de Investigacion</th>
        <th>Lineas de Investigacion</th>
    </tr>
    @foreach($data as $r)
        <tr>
            <td>{{$i++}}</td>
            <td>{{$r['title']}}.</td>
            <td>
                {!! implode('<br>', array_column($r['authors'], 'name')) !!}
            </td>
            <td>{{$r['document']}}</td>
            <td>{{$r['area']}}</td>
            <td>{{$r['group']}}</td>
            <td>{{$r['line']}}</td>
        </tr>
    @endforeach
</table>
