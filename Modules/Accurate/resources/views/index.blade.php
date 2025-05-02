@extends('accurate::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('accurate.name') !!}</p>
@endsection
