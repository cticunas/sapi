<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<style>
    *{ font-size:13px; }
    .header div{
        display: inline-block;
        margin-bottom:-40px;
        margin-top:15px;
    }
    table{
        
    }
    table tr th{
        border-bottom:0.5px solid #000;
        border-top:0.5px solid #000;
        border-right:0.5px solid #000;
        font-size:9.5px;
        width:46px;
        background: #d9d9d9;
    }
    table tr th:first-child{ 
        border-left:0.5px solid #000;
    }
    table tr td{
        text-align:right;
        border-bottom:0.5px solid #000;
        border-right:0.5px solid #000;
        font-size:11px;
        width:46px;
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
                <p style="margin-bottom:-20px; "><img src="{{$header_logo}}" style="width:50px;"/></p></div>
            <div style="width:520px;">
                <p style="text-align:center; margin:0; padding:0px;"><b>REPORTE DE DEUDAS DE LOS PUESTOS</b></p>
                <p style="text-align:center; margin:0; padding:0px;"><b>MERCADO: </b><span style="margin-right:20px">{{$market}}</span><b>SECTOR: </b><span style="margin-right:20px">{{$sector}}</span></p>
                
            </div>
            <div style="margin:0px 0 0 0;">
                <p style="margin:0px 0 0 0; padding:0; font-size:11px;">Fecha: {{date_default_timezone_set("America/Lima")}} {{date("d/m/Y H:i:s", time())}}</p>
                <p style="margin:0 0 5px 0; padding:0; font-size:11px; margin-bottom:0px;">Usuario: {{$username}}</p>
            </div>
        </div>
  
    @foreach($debts_the_stands_by_month as $key=>$stand)
        <div style="margin-top:10px;">
            <span style="margin-right:30px"><b>TITULAR: </b>{{$list_the_stand[$key]['name']}} {{$list_the_stand[$key]['lastname']}}</span>
            <span style="margin-right:30px"><b> PUESTO: </b>{{$list_the_stand[$key]['code']}}</span>
            <span><b> TARIFA: </b>{{$list_the_stand[$key]['rate_amount']}}</span>
        </div>
        <table>
            <thead>
            <tr>
                <th style="width:35px;">AÑO</th>
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
                {{$counter=1}}
                {{$total_debt_per_year=0}}
                {{$sum_total=0}}
                @foreach($stand as $debt)
                    @if($counter==1)
                        <tr>
                            <td style="width:35px;">{{$debt->year}}</td>
                    @endif
                    @while ($counter!=$debt->month)
                        <td>0.00</td>
                        {{$counter=$counter+1}}
                        @if($counter==13)
                            <td>{{number_format($total_debt_per_year,2)}}</td>
                            </tr>
                            {{$counter=1}}
                            {{$sum_total=$sum_total+$total_debt_per_year}}
                            {{$total_debt_per_year=0}}
                            <tr>
                            <td style="width:35px;">{{$debt->year}}</td>
                        @endif
                    @endwhile
                    @if($counter==$debt->month)
                        @if(isset($debt->fractionary_id) || is_numeric(stripos($debt->comment,'fraccio')))
                            <td>{{number_format($debt->total,2)}} <span style="font-size:8px">-F</span></td>
                        @elseif(isset($debt->coactive_id) || is_numeric(stripos($debt->comment,'coactiv')))
                            <td>{{number_format($debt->total,2)}} <span style="font-size:8px">COA</span></td>
                        @elseif($debt->payment===0 && $debt->payment_status===1)
                            <td>{{number_format($debt->total,2)}} <span style="font-size:8px">PRE</span></td>
                        @elseif(isset($debt->previous_total) || is_numeric(stripos($debt->comment,'condonad')))
                            @if(is_numeric(stripos($debt->comment,'condonad')))
                            @else
                                {{$total_debt_per_year=$total_debt_per_year+$debt->total}}
                            @endif
                            <td>{{number_format($debt->total,2)}} <span style="font-size:8px">CON</span></td>
                        @else
                            {{$total_debt_per_year=$total_debt_per_year+$debt->total}}
                            <td>{{number_format($debt->total,2)}}</td>
                        @endif
                        {{$counter=$counter+1}}

                    @endif
                    @if($counter==13)
                        <td>{{number_format($total_debt_per_year,2)}}</td>
                        {{$counter=1}}
                        {{$sum_total=$sum_total+$total_debt_per_year}}
                        {{$total_debt_per_year=0}}
                        </tr>
                    @endif
                @endforeach
                @while($counter!=13 && $counter>1)
                    <td>0.00</td>
                    {{$counter=$counter+1}}
                    @if($counter==13)
                        <td>{{number_format($total_debt_per_year,2)}}</td>
                        </tr>
                        {{$sum_total=$sum_total+$total_debt_per_year}}
                    @endif
                @endwhile
            </tbody>
        </table>
        <div style="text-align:right;">
            <span><b style="font-size:11px;">TOTAL S/. </b>  </span><span> <b style="font-size:12px; padding-right:6px;">{{number_format($sum_total,2)}}</b></span>
        </div>
    @endforeach
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