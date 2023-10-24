@php
    $title_colspan = 4;
    $count = 1;
    $font_size_titles = "font-size:12px; border: 0 solid #ffffff; text-align:left; font-weight:'normal'; padding:0; margin:0;";
    $style_th = "border: 0px solid #404040; background:#404040; color:#fff; font-size:12px;";
    $style_td_with_border_all = "border: 0px solid #ffffff; font-size:12px;";
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<style>
    *{ font-size:13px; }
 
    table tr th{
        border-bottom:0.5px solid #000;
        border-top:0.5px solid #000;
        border-right:0.5px solid #000;
        font-size:12px;
    }
    table tr th:first-child{ 
        border-left:0.5px solid #000;
    }
    table tr td{
       
        border-bottom:0.5px solid #000;
        border-right:0.5px solid #000;
        font-size:10px;
    }
    table tr td:first-child{
       
        border-left:0.5px solid #000;
    }
</style>
<body>
<table>
  <tr>
    <th style="{{$font_size_titles}}" colspan="{{$title_colspan}}">{{$company['name']}}</th>
  </tr>
  <tr>
    <th style="{{$font_size_titles}}" colspan="{{$title_colspan}}">{{$company['city']}} - RUC. {{$company['ruc']}}</th>
  </tr>
  <tr>
    <th style="{{$font_size_titles}}" colspan="{{$title_colspan}}"><b>REPORTES DE RECAUDACION DE COMISIONISTA</b></th>
  </tr>
  <tr> 
    <th style="{{$font_size_titles}}" colspan="{{$title_colspan}}">Generado e{{date_default_timezone_set("America/Lima")}} {{date("d/m/Y H:i:s", time())}}</th>
  </tr>
  <tr> 
    <th style="{{$font_size_titles}}" colspan="{{$title_colspan}}">Del: {{$date_init}} - {{$date_end}}</th>
  </tr>
  <tr> 
    <th style="{{$font_size_titles}}" colspan="{{$title_colspan}}">Por: {{$user['name']}}</th>
  </tr>
  <tr>
    <th style="{{$style_th}}">DETALLE</th>
    <th style="{{$style_th}}">TOTAL RECAUDADO</th> 
    <th style="{{$style_th}}">%</th>
    <th style="{{$style_th}}">COMISIÓN</th>
  </tr>
    {{$tamanio_vector=count($totalByConcepts)}}
    @for ($i = 0; $i < $tamanio_vector; $i++)
        @if($i!=($tamanio_vector-1))
            <tr>
                <td style="{{$style_td_with_border_all}} text-align:center;">{{$totalByConcepts[$i]['sector_name']}}</td>
                <td style="{{$style_td_with_border_all}} text-align:center;">{{$totalByConcepts[$i]['total']}}</td>
                <td style="{{$style_td_with_border_all}} text-align:center;">{{$totalByConcepts[$i]['porcentual']}}</td>
                <td style="{{$style_td_with_border_all}} text-align:right;">{{$totalByConcepts[$i]['calculate']}}</td>   
            </tr>
        @else
            <tr>
                <td style="{{$style_td_with_border_all}} text-align:center; border-top:1px solid #000;">TOTAL</td>
                <td style="{{$style_td_with_border_all}} text-align:center; border-top:1px solid #000;">{{$totalByConcepts[$i]['total']}}</td>
                <td style="{{$style_td_with_border_all}} text-align:center; border-top:1px solid #000;"></td>
                <td style="{{$style_td_with_border_all}} text-align:right; border-top:1px solid #000;">{{$totalByConcepts[$i]['calculate']}}</td>   
            </tr>
        @endif
    @endfor
   
</table>


<script type="text/php">
    if (isset($pdf)) {
        $text = "Pag. {PAGE_NUM} de {PAGE_COUNT}";
        $size = 8;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width);
        //$y = $pdf->get_height() - 35;
        $y = 20;
        $pdf->page_text($x, $y, $text, $font, $size);
    }
</script>
</body>
</html>