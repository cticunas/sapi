@php
    if($fractionary['types_fractionaries']['FRACTIONARY_TYPE_MEC']== $fractionary['fractionary_type_id'] ||
        $fractionary['types_fractionaries']['FRACTIONARY_TYPE_RRC']== $fractionary['fractionary_type_id'] ||
        $fractionary['types_fractionaries']['FRACTIONARY_TYPE_MTD']== $fractionary['fractionary_type_id']){
          $title_colspan = 7;
    }else if($fractionary['types_fractionaries']['FRACTIONARY_TYPE_PIT']== $fractionary['fractionary_type_id'])
        $title_colspan = 8;
    else if($fractionary['types_fractionaries']['FRACTIONARY_TYPE_MAU']== $fractionary['fractionary_type_id'])
        $title_colspan = 5;
    else $title_colspan = 6;
    $font_size_titles = 'font-size:12px; border: 0 solid #ffffff;';
    $style_th = "border: 0px solid #404040; background:#404040; text-align: center; color:#fff; font-size:8px;";
    $style_td_with_border_all = "border: 0px solid #ffffff; font-size:7px;";
@endphp
<table>
  <tr>
    <th style="{{$font_size_titles}}" colspan="{{$title_colspan}}">{{$fractionary['company']['name']}}</th>
  </tr>
  <tr>
    <th style="{{$font_size_titles}}" colspan="{{$title_colspan}}">{{$fractionary['company']['city']}} - RUC. {{$fractionary['company']['ruc']}}</th>
  </tr>
  <tr>
    <th style="{{$font_size_titles}}" colspan="{{$title_colspan}}"><b>REPORTE DE FRACCIONAMIENTO</b></th>
  </tr>
  <tr> 
    <th style="{{$font_size_titles}}" colspan="{{$title_colspan}}">Generado e{{date_default_timezone_set("America/Lima")}} {{date("d/m/Y H:i:s", time())}}</th>
  </tr>
  <tr> 
    <th style="{{$font_size_titles}}" colspan="{{$title_colspan}}">Del: {{$fractionary['report_date']}}</th>
  </tr>
  <tr> 
    <th style="{{$font_size_titles}}" colspan="{{$title_colspan}}">Por: {{$fractionary['user_logged_in']['username']}}</th>
  </tr>
  <tr>
    <th style="{{$style_th}}">N°.</th>
    <th style="{{$style_th}}">FECHA DEL<br>FRACC.</th>
    <th style="{{$style_th}}">-</th>
    <th style="{{$style_th}}">{{$fractionary['types_fractionaries']['FRACTIONARY_TYPE_PIT']== $fractionary['fractionary_type_id'] ? 'PROPIETARIO' : 'TITULAR'}}</th>
    @if($fractionary['types_fractionaries']['FRACTIONARY_TYPE_MEC']== $fractionary['fractionary_type_id'] ||
        $fractionary['types_fractionaries']['FRACTIONARY_TYPE_RRC']== $fractionary['fractionary_type_id'] ||
        $fractionary['types_fractionaries']['FRACTIONARY_TYPE_MTD']== $fractionary['fractionary_type_id'] ||
        $fractionary['types_fractionaries']['FRACTIONARY_TYPE_PIT']== $fractionary['fractionary_type_id'] ||
        $fractionary['types_fractionaries']['FRACTIONARY_TYPE_PNL']== $fractionary['fractionary_type_id'])
      <th style="{{$style_th}}">{{$fractionary['types_fractionaries']['FRACTIONARY_TYPE_PIT']== $fractionary['fractionary_type_id'] ? 'INFRACTOR' : 'CONTRATANTE'}}</th>
    @endif
    
    @if($fractionary['types_fractionaries']['FRACTIONARY_TYPE_PIT']== $fractionary['fractionary_type_id'])
      <th style="{{$style_th}}">CODIGO DE<br> INFRACCION</th>
      <th style="{{$style_th}}">N° PLACA</th>
    @endif
    @if($fractionary['types_fractionaries']['FRACTIONARY_TYPE_MEC']== $fractionary['fractionary_type_id'] || 
        $fractionary['types_fractionaries']['FRACTIONARY_TYPE_MTD']== $fractionary['fractionary_type_id'] || 
        $fractionary['types_fractionaries']['FRACTIONARY_TYPE_RRC']== $fractionary['fractionary_type_id'])
      <th style="{{$style_th}}">PUESTO</th>
    @endif
    @if($fractionary['types_fractionaries']['FRACTIONARY_TYPE_TER']== $fractionary['fractionary_type_id'] ||
        $fractionary['types_fractionaries']['FRACTIONARY_TYPE_NCH']== $fractionary['fractionary_type_id'])
      <th style="{{$style_th}}">{{$fractionary['types_fractionaries']['FRACTIONARY_TYPE_TER']== $fractionary['fractionary_type_id'] ? 'INFORME' : 'UBICACIÓN'}}</th>
    @endif
    <th style="{{$style_th}}">MONTO DE<br> INFRACCION</th>
  </tr>

  @foreach($fractionary['listfractionaries'] as $key=>$fractionarie_data)   
        <tr>
          <td style="{{$style_td_with_border_all}} text-align:center;">{{$key+1}}</td>
          <td style="{{$style_td_with_border_all}} text-align:center;">{{date_format(date_create($fractionarie_data['date_start']),"d/m/Y")}}</td>
          <td style="{{$style_td_with_border_all}}">{{$fractionarie_data['code']}}</td>
          <td style="{{$style_td_with_border_all}}">{{strtoupper($fractionarie_data['titular1_name'])}} {{strtoupper($fractionarie_data['titular1_lastname'])}}</td>
          @if($fractionary['types_fractionaries']['FRACTIONARY_TYPE_MEC']== $fractionary['fractionary_type_id'] ||
        $fractionary['types_fractionaries']['FRACTIONARY_TYPE_RRC']== $fractionary['fractionary_type_id'] ||
        $fractionary['types_fractionaries']['FRACTIONARY_TYPE_MTD']== $fractionary['fractionary_type_id'] ||
        $fractionary['types_fractionaries']['FRACTIONARY_TYPE_PIT']== $fractionary['fractionary_type_id'] ||
        $fractionary['types_fractionaries']['FRACTIONARY_TYPE_PNL']== $fractionary['fractionary_type_id'])
            <td style="{{$style_td_with_border_all}}">{{strtoupper($fractionarie_data['representative_name'])}} {{strtoupper($fractionarie_data['representative_lastname'])}}</td>
          @endif
          @if($fractionary['types_fractionaries']['FRACTIONARY_TYPE_PIT']== $fractionary['fractionary_type_id'])
            <td style="{{$style_td_with_border_all}} text-align:center;">{{$fractionarie_data['description3']}}</td>
            <td style="{{$style_td_with_border_all}} text-align:center;">{{$fractionarie_data['description2']}}</td>
          @endif
          @if($fractionary['types_fractionaries']['FRACTIONARY_TYPE_MEC']== $fractionary['fractionary_type_id'] || 
              $fractionary['types_fractionaries']['FRACTIONARY_TYPE_MTD']== $fractionary['fractionary_type_id'] || 
              $fractionary['types_fractionaries']['FRACTIONARY_TYPE_RRC']== $fractionary['fractionary_type_id'])
            <td style="{{$style_td_with_border_all}} text-align:center;">{{$fractionarie_data['stand_code']}}{{$fractionarie_data['stand_name']}}</td>
          @endif
          @if($fractionary['types_fractionaries']['FRACTIONARY_TYPE_TER']== $fractionary['fractionary_type_id'] ||
              $fractionary['types_fractionaries']['FRACTIONARY_TYPE_NCH']== $fractionary['fractionary_type_id'])
            <td style="{{$style_td_with_border_all}} text-align:center;">{{$fractionarie_data['description']}}</td>
          @endif
          <td style="{{$style_td_with_border_all}} text-align:rigth; font-size:9px;">{{$fractionarie_data['total']}}</td>
        </tr>
    @endforeach
    
</table>