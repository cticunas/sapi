<div>
<style>
    *{ font-size:13px; }
    .header{  padding:0; margin:0 0 -40px 0;}
    .header div{
        display: inline-block;
    }
    .header img{
       /* max-height:60px;
        max-width:100%;*/
        object-fit:contain;
        height:50px;
        width:50px;
    }
    h1{
        font-size:14px;
        text-align:center; padding:0;
        margin:0px 0 0 0;
    }
    .upper{
        text-transform:uppercase;
    }
    table{
        
    }
    table thead th{
        border-bottom:1px solid #ccc;
        border-top:1px solid #ccc;
        background:#ddd;
    }
    table tbody th{ }
    table tfoot th{
        border-top:1px solid #ccc;
        background:#ddd;
    }
    hr{
        background:#ccc;
        border:1px solid #ccc;
        width:97%;
    }
    .note{
        font-size:13px;
    }
    .column-center{
        text-align:center;
    }
    .column-right{
        text-align:right;
    }

    .signatures{
        padding-top:30px;
        padding-bottom:30px;
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
        margin:0; padding:0;
    }
    .signatures .boss .role{
        margin:0;
    }

    .debt-list table{
       margin:auto;
    }
    
    
</style>
    <div class="header">
        <div style="width:80px;"><img src="{{$header_logo}}" /> </div>
        <div style="width:330px;">
            <p style="text-align:center; margin:0; padding:0;">{{$company->name}}</p>
            <p style="text-align:center; margin:0; padding:0;">RUC: {{$company->ruc}}</p>
            <p style="text-align:center; margin:0; padding:0;"><b>Tingo Maria - Perú</b></p>
            <p style="text-align:center; margin:0; padding:0;">{{$company->address}}</p>
        </div>
        <div style="width:180px;"><img src="{{$header_photo}}" /> </div>
    </div>
    <div class="body">
        <h1 class="upper">NOTIFICACION {{$n->code}}</h1>
        @if($n->status==0)
            <p style="padding:0; margin:0; text-align:center;">(SUBSANADO)</p>
        @endif
        <div>
			<p class="column-right">{{$company->city}}, {{ \Carbon\Carbon::parse($n->date_at)->locale('es')->isoFormat('dddd DD \d\e MMMM \d\e\l YYYY') }}</p>
			<p style="text-align:justify">Sr(a). <b>{{$n->to->name}} {{$n->to->lastname}}</b>, identificado con DNI {{$n->to->dni}}, con domicilio en {{$n->to->address2}}
                Se le hace llegar esta notificaci&oacute;n por tener deudas pendientes con nuestra instituci&oacute;n.
      
        
            @if($n->stand_id>0)
                
                    La notificaci&oacute;n corresponde a su Puesto: <b>{{$stand->code}}</b>, sector <b>{{$stand->sector->name}}</b>, giro <b>{{$stand->gyre->name}}</b>, <b>{{$stand->market->name}}</b> ubicado en {{$stand->market->address}}. </span>
                    
                @endif
            </p>
            
            <div class="debt-list">
                <table>
                    <thead> <tr> <th>Periodo</th> <th>Concepto</th> <th>Monto</th>  </tr> </thead>
                    <tbody>
                        @foreach($debts as $debt):
                        <tr>
                            <td>{{ date("Y-m", strtotime($debt->date_at)) }}</td>
                            <td>{{ $n->concept->name }}</td>
                            <td class="column-right">{{number_format($debt->total,2)}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr><th></th> <th>Total</th><th class="column-right">{{number_format($total,2)}}</th> </tr>
                    </tfoot>
                </table>
            </div>
            <div style="padding-top:10px;">Nota:</div>
			<p style="padding:0;margin:0;">{{$footer}}</p>
            
            <div class="signatures" style="{{!$signature_image?'padding-top:100px':''}}">
                <div class="boss">   
                    <img class="signature_image" src="{{$signature_image}}" />
                    <p class="name upper">{{$signature_name}}</p>
                    <p class="role upper"> {{$signature_role}}</p>
                </div>
            </div>
            
		</div>

    </div>

</div>