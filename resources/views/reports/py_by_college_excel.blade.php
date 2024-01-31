<table>
    <tr><td colspan="7"><b>Universidad Nacional Agraria de la Selva</b></td></tr>
    <tr><td colspan="7"><b>Unidad de Investigacion de la UNAS</b></td></tr>
    <tr><td colspan="7"><b>{{$college->name}}</b></td></tr>
    <tr align="center">
        <th>N°</th>
        <th>Titulo</th>
        <th>Autores</th>
        <th>Documento</th>
        <th>Area</th>
        <th>Grupo</th>
        <th>Linea</th>
    </tr>
    @foreach($data as $r)
        <tr>
            <td>{{$i++}}</td>
            <td>{{$r['title']}}.</td>
            <td>
                <!--@foreach( $r["authors"] as $author )  {{$author['name']}} <br />  @endforeach-->
                {!! implode('<br>', array_column($r['authors'], 'name')) !!}
            </td>
            <td>{{$r['document']}}</td>
            <td>{{$r['area']}}</td>
            <td>{{$r['group']}}</td>
            <td>{{$r['line']}}</td>
        </tr>
    @endforeach
</table>
