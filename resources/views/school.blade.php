@extends('layouts.appinternal')

@section('content')
    <dependency-component  personid="{{Auth::user()->person->id}}" isdisabled="true" type="school"></dependency-component>
@endsection
