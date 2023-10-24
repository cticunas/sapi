@extends('layouts.app')

@section('content')
<style>
body{
    background:#001529;
 }
#login-card{
    box-shadow:1px 1px 40px rgba(0,0,0,0.4);
}
.btn-primary{
    box-shadow:1px 5px 20px rgba(86,186,236,0.5);
}
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div id="login-card" class="card">
                <div class="card-body">
                    <div>
                        <img class="logo mx-auto d-block img-fluid" src="{{asset('images/logomuni.png')}}" style="max-width:100px" />
                        <h3 style="text-align:center;margin:0;padding:0">SAT - RENTAS</h3>
                    </div>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group row">
                            
                            <div class="col-md-12" style="margin-bottom:1em; margin-top:5em ">
                                <input id="username" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus placeholder="{{ __('Usuario') }}">
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            
                            <div class="col-md-12">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="{{ __('Password') }}">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary form-control">
                                    {{ __('Ingresar') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
