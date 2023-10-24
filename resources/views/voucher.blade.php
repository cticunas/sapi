<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<style>
    /* print comprobant */
    #payment_print {
        margin-bottom: 20px;
    }

	.payment_print__header {
		display: flex;
		justify-content: space-between;
	}

	.payment_print__header__logo {
		height: 80px;
	}

	.payment_print__header__data  {
		display: flex;
		flex-direction: column;
		font-size: .7em;
	}

	.payment_print__body {
		display: flex;
		flex-direction: column;
	}

	.payment_print__body span {
		line-height: 1.5em;
	}

	.payment_print__body table {
		margin-top: 10px
	}

	.payment_print__body__area {
		text-align: center;
		margin-top: 10px;
	}

	.payment_print__body__title {
		font-size: 1.2em;
		text-align: center;
		margin: 15px 0;
	}

  /** Table footer */
	.table-footer {
		float: right;
		padding-right: 2.8em;
	}
</style>
<body>
<div id="payment_print">
    <div class="payment_print__container">
      <div class="payment_print__header">
        <div class="payment_print__header__logo">
          <img src="/images/logomuni.png" alt="image logo" width="70px">
        </div>
        <div class="payment_print__header__data">
          <!-- <span><b>Numero Operación:</b> 0000000008</span> -->
          <span><b>Caja:</b> 1 <b>Usuario:</b> Admin</span>
        </div>
      </div>
      <div class="payment_print__body"> 
        <span class="payment_print__body__area">MUNICIPAL PROVINCIAL DE LEONCIO PRADO - RUPA</span>
        <span>AV. ALAMEDA PERU 525</span>
        <span>RUC: 2025151551515</span>
        <span>AGENCIA: PALACIO MUNICIPAL AV. ALMEDA PERU N°625</span>
        <h1 class="payment_print__body__title">RECIBO DE CAJA N° </h1>
        <span><b>NOMBRE/RAZON SOCIAL:</b></span>
      
    </div>
    </div>
  </div>
</body>
</html>
