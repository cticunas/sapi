@extends('layouts.appinternal')

@section('content')
    <crud-dependency-component in-place="{{$in}}" personid="{{Auth::user()->person->id}}" type="{{$in}}"  ></crud-dependency-component>
@endsection
