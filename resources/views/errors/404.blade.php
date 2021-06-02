{{--@extends('errors::minimal')--}}
@extends('layouts.app')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Cust not Found'))

@section('content')
    <h1 class="text-danger">Cutomer Not Found! <a href="{{route('details', $customer->id)}}">Go BACK</a></h1>
@endsection
