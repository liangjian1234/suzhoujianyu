<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">

    <title>@yield('base_title')</title>

    <!-- Fonts -->
    <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/main.css').'?version='.rand(1,111111)}}" rel="stylesheet" type="text/css">
</head>
@section('base_body')
@show
<script type="text/javascript" src="{{asset('js/app.js')}}"></script>
@section('base_js')
@show
</html>
