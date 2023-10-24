<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<style>
    *{ font-size:13px; }
    .header div{
        display: inline-block;
    }
    table{
        margin-top:10px;
    }
    table tr th{
        border-bottom:0.5px solid #000;
        border-top:0.5px solid #000;
        border-right:0.5px solid #000;
        font-size:10px;
        background: #d9d9d9;
    }
    table tr th:first-child{ 
        border-left:0.5px solid #000;
    }
    table tr td{
        text-align:right;
        border-bottom:0.5px solid #000;
        border-right:0.5px solid #000;
        font-size:10px;
    }
    table tr td:first-child{
        text-align:left;
        border-left:0.5px solid #000;
    }
    .info-estado-cuenta span{
        margin-right:10px;
    }
</style>
<body>
    <div>
        <div class="header">
            <div style="width:50px;">
                <p style="margin-bottom:0px; "><img src="{{$header_logo}}" style="width:50px;"/></p></div>
            <div style="width:520px;">
                <p style="text-align:center; margin:0; padding:0px;">{{$company->name}}</p>
                <p style="text-align:center; margin:0; padding:1px 0px;">Tingo María - RUC: {{$company->ruc}}</p>
                <p style="text-align:center; margin:0; padding:1px 0px;">REPORTES DE {{$text_debt_or_payment}}</p>
            </div>
            <div style="margin:-20px 0 0 0;">
                <p style="margin:0px 0 0 0; padding:0; font-size:11px;">Fecha: {{$date_impresion}}</p>
                <p style="margin:0 0 5px 0; padding:0; font-size:11px; margin-bottom:0px;">Usuario: {{$username}}</p>
            </div>
        </div>
        <div class="info-estado-cuenta">
            <span><b>Mercado:</b> {{$titular['market']['name']}}</span>
            <span><b>Sector:</b> {{$titular['sector']['name']}}</span>
            <span><b>Giro:</b> {{$titular['gyre']['name']}}</span>
            <span><b>Puesto:</b> {{$titular['code']}}</span>
            <span><b>Tarifa:</b> {{$titular['rate_amount']}}</span>
        </div>
        <div>
            <span><b>Conductor:</b> {{$titular['titular']['lastname']}}, {{$titular['titular']['name']}} </span>
            <span><b>Dni:</b> {{$titular['titular']['dni']}}</span>
            <span><b>Contacto:</b> {{$titular['titular']['phone1']}}</span>
            <span><b>Dirección:</b> {{$titular['location']}}</span>
            <span>
                @if($titular['paymentStatus']==0)
                <b>Deuda:</b>
                @else
                <b>Pago:</b>
                @endif
             S/{{$titular['total']}}</span>
        </div>
        <table>
            <thead>
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
                <th>TOTAL</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list_debts_by_year as $debt)
                <tr>
                    <td style="width:30px; font-size:12px">{{$debt['year']}}</td>
                    <td style="width:47px;">
                        @if(isset($debt[1]['total']))
                            @if(isset($debt[1]['fractionary_id']) || is_numeric(stripos($debt[1]['comment'],'fraccio')))
                                {{$debt[1]['total']}} <span style="font-size:8px">-F</span>
                            @elseif(isset($debt[1]['coactive_id']) || is_numeric(stripos($debt[1]['comment'],'coactiv')))
                                {{$debt[1]['total']}} <span style="font-size:8px">COA</span>
                            @elseif($debt[1]['payment']===0 && $debt[1]['payment_status']===1)
                                {{$debt[1]['total']}} <span style="font-size:8px">PRE</span>
                            @elseif(isset($debt[1]['previous_total']) || is_numeric(stripos($debt[1]['comment'],'condonad')))
                                {{$debt[1]['total']}} <span style="font-size:8px">CON</span>
                            @else
                                {{$debt[1]['total']}}
                            @endif
                        @else
                            
                        @endif
                    </td>
                    <td style="width:47px;">
                        @if(isset($debt[2]['total']))
                            @if(isset($debt[2]['fractionary_id']) || is_numeric(stripos($debt[2]['comment'],'fraccio')))
                                {{$debt[2]['total']}} <span style="font-size:8px">-F</span>
                            @elseif(isset($debt[2]['coactive_id']) || is_numeric(stripos($debt[2]['comment'],'coactiv')))
                                {{$debt[2]['total']}} <span style="font-size:8px">COA</span>
                            @elseif($debt[2]['payment']===0 && $debt[2]['payment_status']===1)
                                {{$debt[2]['total']}} <span style="font-size:8px">PRE</span>
                            @elseif(isset($debt[2]['previous_total']) || is_numeric(stripos($debt[2]['comment'],'condonad')))
                                {{$debt[2]['total']}} <span style="font-size:8px">CON</span>
                            @else
                                {{$debt[2]['total']}}
                            @endif
                        @else
                            
                        @endif
                    </td>
                    <td style="width:47px;">
                        @if(isset($debt[3]['total']))
                            @if(isset($debt[3]['fractionary_id']) || is_numeric(stripos($debt[3]['comment'],'fraccio')))
                                {{$debt[3]['total']}} <span style="font-size:8px">-F</span>
                            @elseif(isset($debt[3]['coactive_id']) || is_numeric(stripos($debt[3]['comment'],'coactiv')))
                                {{$debt[3]['total']}} <span style="font-size:8px">COA</span>
                            @elseif($debt[3]['payment']===0 && $debt[3]['payment_status']===1)
                                {{$debt[3]['total']}} <span style="font-size:8px">PRE</span>
                            @elseif(isset($debt[3]['previous_total']) || is_numeric(stripos($debt[3]['comment'],'condonad')))
                                {{$debt[3]['total']}} <span style="font-size:8px">CON</span>
                            @else
                                {{$debt[3]['total']}}
                            @endif
                        @else
                            
                        @endif
                    </td>
                    <td style="width:47px;">
                    @if(isset($debt[4]['total']))
                        @if(isset($debt[4]['fractionary_id']) || is_numeric(stripos($debt[4]['comment'],'fraccio')))
                            {{$debt[4]['total']}} <span style="font-size:8px">-F</span>
                        @elseif(isset($debt[4]['coactive_id']) || is_numeric(stripos($debt[4]['comment'],'coactiv')))
                            {{$debt[4]['total']}} <span style="font-size:8px">COA</span>
                        @elseif($debt[4]['payment']===0 && $debt[4]['payment_status']===1)
                            {{$debt[4]['total']}} <span style="font-size:8px">PRE</span>
                        @elseif(isset($debt[4]['previous_total']) || is_numeric(stripos($debt[4]['comment'],'condonad')))
                            {{$debt[4]['total']}} <span style="font-size:8px">CON</span>
                        @else
                            {{$debt[4]['total']}}
                        @endif
                    @else
                        
                    @endif
                    </td>
                    <td style="width:47px;">
                    @if(isset($debt[5]['total']))
                        @if(isset($debt[5]['fractionary_id']) || is_numeric(stripos($debt[5]['comment'],'fraccio')))
                            {{$debt[5]['total']}} <span style="font-size:8px">-F</span>
                        @elseif(isset($debt[5]['coactive_id']) || is_numeric(stripos($debt[5]['comment'],'coactiv')))
                            {{$debt[5]['total']}} <span style="font-size:8px">COA</span>
                        @elseif($debt[5]['payment']===0 && $debt[5]['payment_status']===1)
                            {{$debt[5]['total']}} <span style="font-size:8px">PRE</span>
                        @elseif(isset($debt[5]['previous_total']) || is_numeric(stripos($debt[5]['comment'],'condonad')))
                            {{$debt[5]['total']}} <span style="font-size:8px">CON</span>
                        @else
                            {{$debt[5]['total']}}
                        @endif
                    @else
                        
                    @endif
                    </td>
                    <td style="width:47px;">
                    @if(isset($debt[6]['total']))
                        @if(isset($debt[6]['fractionary_id']) || is_numeric(stripos($debt[6]['comment'],'fraccio')))
                            {{$debt[6]['total']}} <span style="font-size:8px">-F</span>
                        @elseif(isset($debt[6]['coactive_id']) || is_numeric(stripos($debt[6]['comment'],'coactiv')))
                            {{$debt[6]['total']}} <span style="font-size:8px">COA</span>
                        @elseif($debt[6]['payment']===0 && $debt[6]['payment_status']===1)
                            {{$debt[6]['total']}} <span style="font-size:8px">PRE</span>
                        @elseif(isset($debt[6]['previous_total']) || is_numeric(stripos($debt[6]['comment'],'condonad')))
                            {{$debt[6]['total']}} <span style="font-size:8px">CON</span>
                        @else
                            {{$debt[6]['total']}}
                        @endif
                    @else
                        
                    @endif
                    </td>
                    <td style="width:47px;">
                    @if(isset($debt[7]['total']))
                        @if(isset($debt[7]['fractionary_id']) || is_numeric(stripos($debt[7]['comment'],'fraccio')))
                            {{$debt[7]['total']}} <span style="font-size:8px">-F</span>
                        @elseif(isset($debt[7]['coactive_id']) || is_numeric(stripos($debt[7]['comment'],'coactiv')))
                            {{$debt[7]['total']}} <span style="font-size:8px">COA</span>
                        @elseif($debt[7]['payment']===0 && $debt[7]['payment_status']===1)
                            {{$debt[7]['total']}} <span style="font-size:8px">PRE</span>
                        @elseif(isset($debt[7]['previous_total']) || is_numeric(stripos($debt[7]['comment'],'condonad')))
                            {{$debt[7]['total']}} <span style="font-size:8px">CON</span>
                        @else
                            {{$debt[7]['total']}}
                        @endif
                    @else
                        
                    @endif
                    </td>
                    <td style="width:47px;">
                    @if(isset($debt[8]['total']))
                        @if(isset($debt[8]['fractionary_id']) || is_numeric(stripos($debt[8]['comment'],'fraccio')))
                            {{$debt[8]['total']}} <span style="font-size:8px">-F</span>
                        @elseif(isset($debt[8]['coactive_id']) || is_numeric(stripos($debt[8]['comment'],'coactiv')))
                            {{$debt[8]['total']}} <span style="font-size:8px">COA</span>
                        @elseif($debt[8]['payment']===0 && $debt[8]['payment_status']===1)
                            {{$debt[8]['total']}} <span style="font-size:8px">PRE</span>
                        @elseif(isset($debt[8]['previous_total']) || is_numeric(stripos($debt[8]['comment'],'condonad')))
                            {{$debt[8]['total']}} <span style="font-size:8px">CON</span>
                        @else
                            {{$debt[8]['total']}}
                        @endif
                    @else
                        
                    @endif
                    </td>
                    <td style="width:47px;">
                    @if(isset($debt[9]['total']))
                        @if(isset($debt[9]['fractionary_id']) || is_numeric(stripos($debt[9]['comment'],'fraccio')))
                            {{$debt[9]['total']}} <span style="font-size:8px">-F</span>
                        @elseif(isset($debt[9]['coactive_id']) || is_numeric(stripos($debt[9]['comment'],'coactiv')))
                            {{$debt[9]['total']}} <span style="font-size:8px">COA</span>
                        @elseif($debt[9]['payment']===0 && $debt[9]['payment_status']===1)
                            {{$debt[9]['total']}} <span style="font-size:8px">PRE</span>
                        @elseif(isset($debt[9]['previous_total']) || is_numeric(stripos($debt[9]['comment'],'condonad')))
                            {{$debt[9]['total']}} <span style="font-size:8px">CON</span>
                        @else
                            {{$debt[9]['total']}}
                        @endif
                    @else
                        
                    @endif
                    </td>
                    <td style="width:47px;">
                    @if(isset($debt[10]['total']))
                        @if(isset($debt[10]['fractionary_id']) || is_numeric(stripos($debt[10]['comment'],'fraccio')))
                            {{$debt[10]['total']}} <span style="font-size:8px">-F</span>
                        @elseif(isset($debt[10]['coactive_id']) || is_numeric(stripos($debt[10]['comment'],'coactiv')))
                            {{$debt[10]['total']}} <span style="font-size:8px">COA</span>
                        @elseif($debt[10]['payment']===0 && $debt[10]['payment_status']===1)
                            {{$debt[10]['total']}} <span style="font-size:8px">PRE</span>
                        @elseif(isset($debt[10]['previous_total']) || is_numeric(stripos($debt[10]['comment'],'condonad')))
                            {{$debt[10]['total']}} <span style="font-size:8px">CON</span>
                        @else
                            {{$debt[10]['total']}}
                        @endif
                    @else
                        
                    @endif
                    </td>
                    <td style="width:47px;">
                    @if(isset($debt[11]['total']))
                        @if(isset($debt[11]['fractionary_id']) || is_numeric(stripos($debt[11]['comment'],'fraccio')))
                            {{$debt[11]['total']}} <span style="font-size:8px">-F</span>
                        @elseif(isset($debt[11]['coactive_id']) || is_numeric(stripos($debt[11]['comment'],'coactiv')))
                            {{$debt[11]['total']}} <span style="font-size:8px">COA</span>
                        @elseif($debt[11]['payment']===0 && $debt[11]['payment_status']===1)
                            {{$debt[11]['total']}} <span style="font-size:8px">PRE</span>
                        @elseif(isset($debt[11]['previous_total']) || is_numeric(stripos($debt[11]['comment'],'condonad')))
                            {{$debt[11]['total']}} <span style="font-size:8px">CON</span>
                        @else
                            {{$debt[11]['total']}}
                        @endif
                    @else
                        
                    @endif
                    </td>
                    <td style="width:47px;">
                    @if(isset($debt[12]['total']))
                        @if(isset($debt[12]['fractionary_id']) || is_numeric(stripos($debt[12]['comment'],'fraccio')))
                            {{$debt[12]['total']}} <span style="font-size:8px">-F</span>
                        @elseif(isset($debt[12]['coactive_id']) || is_numeric(stripos($debt[12]['comment'],'coactiv')))
                            {{$debt[12]['total']}} <span style="font-size:8px">COA</span>
                        @elseif($debt[12]['payment']===0 && $debt[12]['payment_status']===1)
                            {{$debt[12]['total']}} <span style="font-size:8px">PRE</span>
                        @elseif(isset($debt[12]['previous_total']) || is_numeric(stripos($debt[12]['comment'],'condonad')))
                            {{$debt[12]['total']}} <span style="font-size:8px">CON</span>
                        @else
                            {{$debt[12]['total']}}
                        @endif
                    @else
                        
                    @endif
                    </td>   
                    <td style="width:46px; font-size:11px">{{$debt['total']}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="display: inline-block;">
            <div style="text-align:right;">
                <span><b>Suma Total: </b></span> <span>{{$total_sum_of_debts}}</span>
            </div>
            <p style="margin-top:-14px;">{{$is_text_debts_direct_fine}}</p>
        </div>
    </div>


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