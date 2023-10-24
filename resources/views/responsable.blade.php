@extends('layouts.appinternal')

@section('content')
    <responsable-component in-place="{{$in}}" personid="{{Auth::user()->person->id}}" isdisabled="true"  ></responsable-component>
@endsection
