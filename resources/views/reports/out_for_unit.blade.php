@extends( $format=='word'?'layouts.word':'layouts.pdf' )
@section('content')
  <h4 style="text-transform: uppercase "> {{$faculty_name}}</h4>
  <p>Informe de investigaciones  del {{$period_type}} {{$period}} - {{$year}}</p>
  <p>Numero de proyectos presentados: {{$total}}</p>
  <p style="width:100%; background: #333; color: white; text-align:center; margin:0">Participantes:</p>
  <table style="width: 100%;">
   <tr style="background: #eee">
     <th style="width: 33%;" align="center">Docentes ({{count($professors)}})</th>
     <th style="width: 33%;" align="center">Administrativos ({{count($workers)}})</th>
     <th style="width: 34%;" align="center">Estudiantes ({{count($students)}})</th>
  </tr> 
  <tr>
    <td>
      @foreach($professors as $r)<p style="margin:2px;">- {{$r['name']}}</p>@endforeach
    </td>
    <td>
      @foreach($workers as $r)<p style="margin:2px;">- {{$r['name']}}</p>@endforeach
    </td>
    <td>
      @foreach($students as $r)<p style="margin:2px;">- {{$r['name']}} ({{$r['dni']}})</p>@endforeach
    </td>
  </tr>
  </table>
  <p style="width:100%; background: #333; color: white; text-align:center; margin:0">Trabajos:</p>
  @foreach($data as $research_by_line)
  <table style="width: 100%;">
  <tr style="background: #eee; border-bottom:1px dashed #333">
    <td colspan="6" style="margin:0; padding:5px; text-transform:uppercase"> Grupo: {{$research_by_line['line']}} </td>
  </tr>
   <tr style="background: #eee">
     <th style="width: 38%;" align="left">Titulo</th>
     <th style="width: 20%;" align="left">Autores</th>
     <th style="text-align:center">Tipo</th>
     <th style="text-align:center" >Inicio - Fin</th>
     <th style="text-align:center">Documento</th>
     <th style="text-align:center">Obs.</th>
  </tr> 
    @foreach($research_by_line['research'] as $r)
    <tr>
      <td style="padding:2px 0">{{$r['name']}}.</td>
      <td style="font-size: 14px">
      @foreach( $r["authors"] as $author )  
      <span>{{$author['name']}} ({{$author['type']}})  {{$author['role']=='Titular'?'(T)': ($author['role']=='Asesor'?' (A)': '') }} </span> <br />  
      @endforeach  
      </td>
      <td style="text-align:center">{{$r['type_research']}}</td>
      <td style="text-align:center">{{$r['date_init']}} {{$r['date_end']}}</td>
      <td style="text-align:center">{{$r['document']}}</td>
      <td style="text-align:center">{{$r['outcome']}}</td>
 </tr>
  @endforeach
  </table>
  @endforeach
  @endsection