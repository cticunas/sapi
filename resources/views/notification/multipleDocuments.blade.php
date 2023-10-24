<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<style>
    *{ font-size:13px; }
    .header{  padding:0; margin:0px 0 0px 0;
        height:80px;
    }
    .header div{
        display: inline-block;
    }
    .header img{
        /*max-height:100px;
        max-width:100%;*/
        
    }
    h1{
        font-size:14px;
        text-align:center;
    }
    .upper{
        text-transform:uppercase;
    }
    .date-people p{display: inline-block;}
    .container-info-people{
        border: 1px solid #000;
        padding:3px 8px 3px 8px;
    }
    table tr th{
        text-align:center;
        /*background:#ccc;*/
        border-left: 1px solid #000;
        border-bottom: 1px solid #000;
        border-top: 1px solid #000;
        font-size:12px;
    }
    table tr th:last-child{
        border-right: 1px solid #000;
    }
    table tr td{
       
        text-align:right;
        border-left: 1px solid #000;
        border-bottom: 1px solid #000;
        font-size:12px;
        width:45px;
    }
    table tr td:first-child{
        text-align:left;
    }
    table tr td:last-child{
        border-right: 1px solid #000;
    }
    hr{
        background:#ccc;
        border:1px solid #ccc;
        width:97%;
    }
    .note{
        font-size:12px;
    }
    .column-center{
        text-align:center;
    }
    .column-right{
        text-align:right;
    }
    .signatures{
        padding-top:10px;
    }
    .signature_image{
        width:100px;
    }
    .signatures .boss{
        display:inline-block;
        width:60%;
        text-align:center;
    }
    .signatures .boss .name{
        border-top:1px dashed #ccc;
        margin:0;
    }
    .signatures .boss .role{
        margin:0;
    }

    .debt-list table{
       margin:auto;
    }
    .page {
       page-break-after: always;
       /*page-break-before: always;
       counter-reset: page 1;*/
    }
    .page:last-child {
       page-break-after: unset;
    }
    
</style>
<body>
<script type="text/php">
    if (isset($pdf)) {
        $text = "Pag. {PAGE_NUM} de {PAGE_COUNT}";
        $size = 8;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width);
        //$y = $pdf->get_height() - 35;
        $y = 30;
        $pdf->page_text($x, $y, $text, $font, $size);
    }
</script>
@foreach($notifications as $notification)
    <div style="margin-top:20px;" class="page">
        
        <div class="header">
            <div style="width:56px;">
                <div styele="">
                    <p style="margin-bottom:-18px"><img src="{{$header_logo}}" style="width:70px;"/></p>
                    <p style="text-align:center; margin:0; padding:0; font-size:4.5px; width:70px;margin-top:18px;">MUNICIPALIDAD PROVINCIAL</p>
                    <p style="text-align:center; margin:0; padding:0; font-size:4.5px; width:70px;">DE LEONCIO PRADO</p>
                </div>
                
            </div>
            <div style="width:520px; margin-top:-40px">
                <p style="text-align:center; margin:0; padding:0; font-size:13px;">{{$company->name}}</p>
                <p style="text-align:center; margin:0; padding:0; font-size:13px;">RUC: {{$company->ruc}}</p>
                <!--<p style="text-align:center; margin:0; padding:0; font-size:13px;"><b>Tingo Maria - Perú</b></p>-->
                <p style="text-align:center; margin:0; padding:0; font-size:13px;">{{$company->address}}</p>
            </div>
            <div style="width:120px; margin-top:0px;">
                <p style="text-align:right; margin:0; padding:0; font-size:10px;">Fecha: {{date("d/m/Y", time())}}</p>
                <p style="text-align:right; margin:0 0 5px 0; padding:0; font-size:10px;">Usuario: {{$username}}</p>
                <div style="width:120px;border:1px solid #ccc; margin-top:0px;">
                    <p style="text-align:center; margin:0; padding:3px 0px; border-bottom:1px solid #ccc; background:#ddd;">AVISO</p>
                    <p style="text-align:center; margin:0; padding:3px 0px;">{{substr($notification['code'], 7, -5)}}</p>
                </div>
                
            </div>
        </div>

        <div class="body" style="margin-top:0px;">
            <h1 class="upper" style="margin:-25px 0 40px -60px">NOTIFICACION - {{$notification['concept']['name']}}</h1>
            <div>
                <!--<p class="column-right">{{$company->city}}, {{ \Carbon\Carbon::parse($notification['date_at'])->locale('es')->isoFormat('dddd DD \d\e MMMM \d\e\l YYYY') }}</p>-->
                <div class="container-info-people">
                    <p style="margin:0; padding:3px 0px;">IDENTIFICACIÓN DEL DEUDOR TRIBUTARIO</p>
                    <div class="date-people">
                        <p style="width:134px; margin:0; padding:3px 0px; text-align:right;">Nombre o Razón Social:</p>
                        <p style="margin:0; padding:1px 0px;"><b style="padding-left:5px">{{$notification['to']['name']}} {{$notification['to']['lastname']}}</b></p>
                    </div>
                    <div class="date-people">
                        <p style="width:134px; margin:0; padding:3px 0px; text-align:right;">Documento de Identidad:</p>
                        <p style="margin:0; padding:1px 0px;"><b style="padding-left:5px">{{$notification['to']['dni']}}</b></p>
                    </div>
                    <div class="date-people">
                        <p style="width:134px; margin:0; padding:3px 0px; text-align:right;">Domicilio Fiscal: </p>
                        <p style="margin:0; padding:1px 0px;"><b style="padding-left:5px">{{$notification['to']['address1']}}</b></p>
                    </div>
                    @if($notification['concept']['id']==$concepts['PIT_ID'] || $notification['concept']['id']==$concepts['TER_ID'] ||
                    $notification['concept']['id']==$concepts['MAU_ID'] || $notification['concept']['id']==$concepts['PNL_ID'] || 
                    $notification['concept']['id']==$concepts['NCH_ID'])
                    <div class="date-people">
                        <p style="width:134px; margin:0; padding:3px 0px; text-align:right;">Concepto: </p>
                        <p style="margin:0; padding:1px 0px;"><b style="padding-left:5px">{{$notification['stand']['sector']['name']}}</b></p>
                    </div>
                    @elseif ($notification['stand_id']>0 && isset($notification['stand']['code']))
                    <div class="date-people">
                        <p style="width:134px; margin:0; padding:3px 0px; text-align:right;">Sector: </p>
                        <p style="margin:0; padding:1px 0px;"><b style="padding-left:5px">{{$notification['stand']['sector']['name']}}</b></p>
                    </div>
                    <div class="date-people">
                        <p style="width:134px; margin:0; padding:3px 0px; text-align:right;">Puesto: </p>
                        <p style="margin:0; padding:1px 0px;"><b style="padding-left:5px">{{$notification['stand']['code']}}</b></p>
                    </div>
                    @else
                    <div class="date-people">
                        <p style="width:134px; margin:0; padding:3px 0px; text-align:right;">Local: </p>
                        <p style="margin:0; padding:1px 0px;"><b style="padding-left:5px">{{$notification['stand']['name']}}</b></p>
                    </div>
                    @endif
                </div>
                <p style="margin:3px 0px; padding:0px 0px;">Estimado Contribuyente:</p>

                <p style="margin:3px 0px 5px 0px; padding:0px 0px;">{{$notification['content']}}</p>
                <!--
                <p style="text-align:justify">Sr(a). <b>{{$notification['to']['name']}} {{$notification['to']['lastname']}}</b>, identificado con DNI {{$notification['to']['dni']}}, con domicilio en {{$notification['to']['address2']}}
                    Se le hace llegar esta notificaci&oacute;n por tener deudas pendientes con nuestra instituci&oacute;n.
                @if($notification['concept']['id']==$concepts['PIT_ID'] || $notification['concept']['id']==$concepts['TER_ID'] ||
                $notification['concept']['id']==$concepts['MAU_ID'] || $notification['concept']['id']==$concepts['PNL_ID'] || 
                $notification['concept']['id']==$concepts['NCH_ID']) 
                    La notificación corresponde a: <b>{{$notification['concept']['name']}}.</b>
                @elseif ($notification['stand_id']>0 && isset($notification['stand']['code']))
                    La notificaci&oacute;n corresponde a su Puesto: <b>{{$notification['stand']['code']}}</b>, sector <b>{{$notification['stand']['sector']['name']}}</b>, giro <b>{{$notification['stand']['gyre']['name']}}</b>, <b>{{$notification['stand']['market']['name']}}</b> ubicado en {{$notification['stand']['market']['address']}}. </span>
                @else
                    La notificaci&oacute;n corresponde a su Local: <b>{{$notification['stand']['name']}}</b> ubicado en {{$notification['stand']['location']}}. </span>
                @endif
                </p>-->

                <table>
                    <tr>
                        <th>AÑO</th>
                        <th>ENE</th>
                        <th>FEB</th>
                        <th>MAR</th>
                        <th>ABR</th>
                        <th>MAY</th>
                        <th>JUN</th>
                        <th>JUL</th>
                        <th>AGO</th>
                        <th>SET</th>
                        <th>OCT</th>
                        <th>NOV</th>
                        <th>DIC</th>
                        <th>DEUDA</th>
                    </tr>
                    {{$counter=1}}
                    {{$total_debt_per_year=0}}
                    @foreach($notification['debts'] as $debt)
                        @if($debt['stand_id']==$notification['stand_id'])//filtro para que imprima deuda segun el puesto
                            @if($counter==1)
                                <tr>
                                <td>{{date("Y", strtotime($debt['date_at']))}}</td>
                            @endif
                            @while ($counter!=date("m", strtotime($debt['date_at'])))
                                <td>0.00</td>
                                {{$counter=$counter+1}}
                                @if($counter==13)
                                    <td>{{number_format($total_debt_per_year,2)}}</td>
                                    </tr>
                                    {{$counter=1}}
                                    {{$total_debt_per_year=0}}
                                    <tr>
                                    <td>{{date("Y", strtotime($debt['date_at']))}}</td>
                                @endif
                            @endwhile
                            @if($counter==date("m", strtotime($debt['date_at'])))
                                {{$total_debt_per_year=$total_debt_per_year+number_format($debt['total'],2)}}
                                <td>{{number_format($debt['total'],2)}}</td>
                                {{$counter=$counter+1}}
                            @endif
                            @if($counter==13)
                            <td>{{number_format($total_debt_per_year,2)}}</td>
                                {{$counter=1}}
                                {{$total_debt_per_year=0}}
                                </tr>
                            @endif
                        @endif
                    @endforeach

                    @while($counter!=13 && $counter>1)
                        <td>0.00</td>
                        {{$counter=$counter+1}}
                        @if($counter==13)
                        <td>{{number_format($total_debt_per_year,2)}}</td>
                            </tr>
                        @endif
                    @endwhile
                </table>
                <div style="text-align:right;">
                    <span><b style="font-size:11px;">TOTAL S/. </b>  </span><span> <b style="font-size:12px; padding-right:4px;">{{ number_format( array_reduce($notification['debts'],function($accu,$debt){ 
                                return $accu += $debt['total'];  
                            },0) ,2) }}</b></span>
                </div>
                <!--
                <div class="debt-list">
                    <table>
                        <thead> <tr> <th>Periodo</th> <th>Concepto</th> <th>Monto</th>  </tr> </thead>
                        <tbody>
                            @foreach($notification['debts'] as $debt)
                            <tr>
                                <td style="text-align: center;">{{ date("Y-m", strtotime($debt['date_at'])) }}</td>
                                <td style="text-align: center;">{{ $notification['concept']['name'] }}</td>
                                <td class="column-right" style="padding-right: 10px;">{{number_format($debt['total'],2)}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th> 
                                <th>Total</th>
                                <th class="column-right" style="padding-right: 10px;">
                                    {{ number_format( array_reduce($notification['debts'],function($accu,$debt){ 
                                        return $accu += $debt['total'];  
                                    },0) ,2) }}
                                </th> 
                            </tr>
                        </tfoot>
                    </table>
                </div> 
                -->
                <div style="padding-top:12px;">Atentamente:</div>
                <div class="signatures" style="{{!$signature_image?'padding-top:100px':''}}">
                    <div class="boss">   
                        <img class="signature_image" src="{{$signature_image}}" />
                        <p class="name upper">{{$signature_name}}</p>
                        <p class="role upper"> {{$signature_role}}</p>
                    </div>
                </div>
                <div style="padding-top:12px;">Avisos:</div>
                <p style="padding:0;margin:0 0 15px 0;">{!! $footer !!}</p>
                
            </div>
        </div>
    </div>
@endforeach
     
    

</body>
</html>
