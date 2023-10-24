<div>
<style>
    *{ font-size:12px; }
    .header{ /*padding:0; margin:0;*/}
    .header div{
        display: inline-block;
    }
    .header img{
        /*max-height:100px;
        max-width:100%;*/
        object-fit:contain;
      /*  height:60px;
        width:60px;*/
    }
    .header div p{
        font-size:13px;
    }
    h1{
        font-size:14px;
        text-align:center; padding:0; margin:0px 0 0px 0;
    }
    
    .upper{
        text-transform:uppercase;
    }
    table{
        width:100%;
    }
    table thead th{
        border-bottom:1px solid #000;
        border-top:1px solid #000;
        border-left: 1px solid #000;
        /*background:#fafafa;*/
        background: #d9d9d9;
        padding: 2px 0px;
    }
    table thead th:last-child{
        border-right: 1px solid #000;
    }
    table tbody tr td{
        padding: 2px 0px;
        border-bottom:1px solid #000;
        border-left: 1px solid #000;
    }
    table tr td:last-child{
        border-right: 1px solid #000;
    }
    table tfoot th{
        padding: 2px 0px;
        border-bottom:1px solid #000;
        border-left: 1px solid #000;
    }
    table tfoot th:last-child{
        border-right: 1px solid #000;
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
    .blocks{
        margin-top:20px;
    }
    .container-border{
        padding: 20px 5px 0px 5px; 
        border: 1px solid #000;
    }
    .blocks .block{
       margin:5px 0 5px 0;
    }
    .block .col{
        display:inline-block;
        width:49%;
        margin:0;
        padding: 0;
    }
    .block .col label{
        /*width:75px;*/
        width:100px;
        display:inline-block;
        padding:2px;
        margin:0;
        font-weight:bold;
    }
    .block .col span{
        /*width:250px;*/
        width:225px;
        display:inline-block;
        padding:2px;
        border-bottom:1px solid #ccc;
        margin:0;
    }
    
    .block p{margin:0;padding:0;}
    .block-1 .col div{margin: 5px 0;}
    .block-6{
     
        border-radius:5px;
        padding:10px;
        padding-top:30px;
    }
    .block-6 .col p.label{
        font-weight:bold;
    }
    .block-6 .col{
        width:18%;
        padding:10px 19px 10px 0;
        margin-right:15px;
    }
    .block-6 .col.large{
        width:27%;
    }
    .block-6 .col .span{
        padding:5px 10px;
        margin:0;
        border-bottom:1px solid #ccc;
    }
    .block-6 .col .span.right{
        text-align:right;
    }
    .signatures .titular {
        margin-right: 30px;
    }
    .signatures .representative,
    .signatures .titular{
        display:inline-block;
        width:40%;
        text-align:center;
    }
    .signatures .representative .name,
    .signatures .titular .name{
        border-top:1px dashed #ccc;
        margin:0;
    }
    .signatures .representative .dni,
    .signatures .titular .dni{
        margin:0;
    }
    .no-visible{
        display:none; 
    }
    .style-address{ 
        text-transform: uppercase;
        font-size:8px;
    }
    
</style>
<div>
    <div>
        <p style="margin:0; padding:0; font-size:10px;">Fecha: {{date("d/m/Y", time())}}</p>
        <p style="margin:0 0 5px 0; padding:0; font-size:10px; margin-bottom:-10px;">Usuario: {{$username}}</p>
    </div>
    <div class="header">
        <div style="width:70px;">
            <p style="margin-bottom:-10px; margin-top:40px;"><img src="{{$header_logo}}" style="width:65px;"/></p></div>
        <div style="width:520px;">
            <p style="text-align:center; margin:0; padding:1px 0px;">{{$name_muni}}</p>
            <p style="text-align:center; margin:0; padding:1px 0px;">RUC: {{$ruc_muni}}</p>
            <p style="text-align:center; margin:0; padding:1px 0px;"><b>Tingo Maria - Perú</b></p>
            <p style="text-align:center; margin:0; padding:1px 0px;">{{$address_muni}}</p>
        </div>
        <div style="width:80px;"><p style="margin-bottom:2px;"><img src="{{$header_photo}}" style="width:90px;"/></p> </div>
    </div>
    <div class="body">
        <h1 class="upper">CONVENIO FRACCIONAMIENTO DE {{$f->concept->name}} {{$f->code}}</h1>
                    @if($f->status==0)
                        <p style="padding:0; margin:0; text-align:center;">(ANULADO)</p>
                    @endif
        <div class="blocks">
            <div class="container-border">
                <div class="block">
                    <div class="col"><label>TITULAR:</label> <span class="upper">{{$f->titular->name}} {{$f->titular->lastname}}</span></div>
                    @if($is_not_contratante)
                        <div class="col"><label>{{$name_representante}}:</label> <span  class="upper">{{$f->representative->name}} {{$f->representative->lastname}} </span></div>
                    @endif
                </div>
                <div class="block">
                    <div class="col"><label style="width:100px;">DNI:</label> <span style="width:50px;">{{$f->titular->dni}}</span>
                            <label style="width:30px; margin-left:30px">CEL:</label> <span style="width:70px;">{{$f->titular->phone1}}</span></div>
                    @if($is_not_contratante)
                    <div class="col"><label style="width:100px;">DNI:</label> <span style="width:50px;">{{$f->representative->dni}}</span>
                            <label style="width:30px; margin-left:30px">CEL:</label> <span style="width:70px;">{{$f->representative->phone1}}</span></div>
                    @endif
                </div>
                <div class="block">
                    @if($f->titular->address1)
                        <div class="col"><label>DOMICILIO:</label> <span class="style-address">{{$f->titular->address1}}</span></div>
                    @endif
                    @if($is_not_contratante)
                        @if($f->representative->address1)
                            <div class="col"><label>DOMICILIO:</label> <span class="style-address">{{$f->representative->address1}}</span></div>
                        @endif       
                    @endif
                    
                </div>
                <div class="block">
                    @if($f->titular->email)
                    <div class="col"><label>CORREO:</label> <span>{{$f->titular->email}}</span></div>
                    @endif
                    @if($is_not_contratante)
                        @if($f->representative->email)
                            <div class="col"><label>CORREO:</label> <span>{{$f->representative->email}}</span></div>
                        @endif
                    @endif
                </div>
                @if($is_type_fra_transito)
                    @if($f->titular2_id)
                    <div class="block">
                        <div class="col"><label>TITULAR 2:</label><span class="upper">{{$f->titular2->name}} {{$f->titular2->lastname}}</span></div>
                        <div class="col"><label style="width:130px;">PLACA DE RODAJE:</label> <span style="width:190px;">{{$f->description2}}</span></div>
                    </div>
                    <div class="block">
                        <div class="col"><label style="width:100px;">DNI:</label> <span style="width:50px;">{{$f->titular2->dni}}</span>
                            <label style="width:30px; margin-left:30px">CEL:</label> <span style="width:70px;">{{$f->titular2->phone1}}</span></div>
                        <div class="col"><label>INFRACCIÓN:</label> <span >{{$f->description3}}</span></div>
                    </div>
                    <div class="block">
                        @if($f->titular2->address1)
                        <div class="col"><label>DOMICILIO:</label> <span class="style-address">{{$f->titular2->address1}}</span></div>
                        @endif
                        <div class="col"><label>PIT N°:</label> <span>{{$f->description}}</span></div>
                    </div>
                    <div class="block">
                        @if($f->titular2->email)
                        <div class="col"><label>CORREO:</label> <span>{{$f->titular2->email}}</span></div>
                        @endif
                    </div>
                    @else
                    <div class="block">
                        <div class="col"><label style="width:130px;">PLACA DE RODAJE:</label> <span style="width:190px;">{{$f->description2}}</span></div>
                        <div class="col"><label>INFRACCIÓN:</label> <span >{{$f->description3}}</span></div>
                    </div>
                    <div class="block">
                        <div class="col"><label>PIT N°:</label> <span>{{$f->description}}</span></div>
                    </div>
                    @endif
                @endif

                @if($f->stand_id)
                <div class="block">
                    <div class="col"><label>PUESTO/LOCAL:</label> <span>{{$stand_code}}</span></div>
                </div>
                @endif
                @if($is_venta_terreno)
                <div class="block">
                    <div class="col"><label>{{$is_venta_terreno}}:</label> <span>{{$f->description}}</span></div>
                </div>
                @endif

                <div class="block">
                    <div class="col"><label>CONCEPTO:</label><span>{{$f->concept->code}} {{$f->concept->name}}</span></div>
                    <div class="col" style="{{$f->reference_id ? '' : 'display:none'}}"><label>REFERENCIA:</label> <span>{{$f->reference_description}}</span></div>
                </div>
                <div class="block">
                    <div class="col"><label>F. INICIO:</label> <span>{{$f->date_start}}</span> </div>
                    <div class="col" style="{{$f->apply_date ? '' : 'display:none'}}"><label>F. APLICACIÓN:</label><span>{{$f->apply_date}}</span></div>
                </div>
                <div class="block">
                    <div class="col"><label>COUTAS:</label><span>{{$f->quotes}}</span></div>
                    <div class="col"><label>INTERES:</label><span>{{$f->interest}}</span></div>
                </div>

            </div>
                
            <table style="margin: 10 0;">
                <thead>
                <tr> <th colspan="3">Deuda</th> <th colspan="3">Rec/Num:</th> <th colspan="3">C. Inicial</th><th colspan="3">Saldo a Fracc.</th></tr>
                </thead>
                <tbody>
                    <tr> 
                        <td colspan="3" class="column-center" style="width:25%;">{{number_format($f->debt,2)}}</td>
                        <td colspan="3" class="column-center" style="width:25%;">{{$f->initial_voucher}}</td>
                        <td colspan="3" class="column-center" style="width:25%;">{{number_format($f->initial_amount,2)}}</td>
                        <td colspan="3" class="column-center" style="width:25%;">{{number_format($f->fractionary_amount,2)}}</td>
                    </tr>
                </tbody>
            </table>
            
            <!--<div class="block block-6">
                <div class="col"><p class="label">Deuda:</p> <p class="span right">{{number_format($f->debt,2)}}</p>  </div>
                <div class="col large"><p class="label">Rec/Num:</p> <p class="span">{{$f->initial_voucher}}</p>  </div>
                <div class="col"><p class="label">C. Inicial:</p> <p class="span right">{{number_format($f->initial_amount,2)}}</p>  </div>
                <div class="col"><p class="label">Saldo a Fracc:</p> <p class="span right">{{number_format($f->fractionary_amount,2)}}</p>  </div>
            </div>-->
        </div>

        <table>
            <thead>
            <tr> <th>Periodo</th> <th>Fecha</th> <th>Saldo</th><th>Interes</th><th>Amort.</th><th>Pago</th> <th>-</th> </tr>
            </thead>
            <tbody>
            @foreach($schedule as $i => $item)
                <tr> <td class="column-center" style="width:10%;">{{$i+1}}</td>
                <td class="column-center" style="width:15%;">{{date("d/m/Y", strtotime($item->date_at)) }}</td>
                <td class="column-right" style="width:15%; padding-right:8px">{{number_format($item->pending_amount,2)}}</td>
                <td class="column-right" style="width:15%; padding-right:8px">{{number_format($item->interest,2)}}</td>
                <td class="column-right" style="width:15%; padding-right:8px">{{number_format($item->amort,2)}}</td>
                <td class="column-right" style="width:15%; padding-right:8px">{{number_format($item->total,2)}}</td>
                <td  class="column-center" style="width:15%;">{{$item->debt->payment_status==1?'Pagado':'Pendiente'}}</td></tr>
            @endforeach;
             </tbody>
            <tfoot>
            <tr> <th></th> <th></th> <th></th> 
            <th class="column-right" style="padding-right:8px;">{{number_format($f->total_interest,2)}}</th>
            
            <th class="column-right" style="padding-right:8px;">{{number_format($f->fractionary_amount,2)}}</th>
            <th class="column-right" style="padding-right:8px;">{{number_format($f->total,2)}}</th>
            <th></th>
             </tr>
            </tfoot>
            
            
        </table>
       
        <div class="note" style="margin-bottom:100px;">
            <div>NOTA:</div>
            <!--<div>Me comprometo a cumplir con el pago de las coutas de presente fraccionamiento.
                La no cancelación de dos(02) coutas, hara que quede SIN EFECTO el presente contrato procediendose a cobrar por via coactiva.</div>-->
            <div><p style="padding:0;margin:0 0 0px 0;">{{$f->comment}}</p></div>
        </div>
        <div class="signatures">
            @if ($f->is_titular_signature === 1)
            <div class="titular">    
                <p class="name upper">{{$f->titular->name}} {{$f->titular->lastname}}</p>
                <p class="dni">DNI: {{$f->titular->dni}}</p>
                <p class="dni">TITULAR</p>
            </div>
            @endif
            @if ($f->is_representative_signature === 1)
            <div class="representative">    
                <p class="name upper">{{$f->representative->name}} {{$f->representative->lastname}}</p>
                <p class="dni">DNI: {{$f->representative->dni}}</p>
                <p class="dni">{{$is_type_fra_transito ? 'INFRACTOR':'CONTRATANTE'}}</p>
            </div>
            @endif
        </div>
        <div class="note">
            <div><b>Base Legal:</b></div>
            <!--<div>Me comprometo a cumplir con el pago de las coutas de presente fraccionamiento.
                La no cancelación de dos(02) coutas, hara que quede SIN EFECTO el presente contrato procediendose a cobrar por via coactiva.</div>-->
            <div><p style="padding:0;margin:0;">{!!$footer!!}</p></div>
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
            $y = 30;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
      </script>
</div>