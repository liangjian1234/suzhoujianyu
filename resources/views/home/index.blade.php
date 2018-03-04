@extends('home._base')

@section('base_title','首页')

@section('base_body')
<body class="body-index">
<div class="container-fluid">
    <div class="index-title">
        <div class="row">
            <div class="col-md-12 text-center f_4 text-white">
                苏州监狱图书预约系统
            </div>
        </div>
    </div>
    <div class="index-list">
        <div class="row">
            <div class="col-md-3 text-center col-md-offset-3">
                <div class="list-item bg-blue center-block" onclick="location.href='{{route('search')}}'">
                    <p class="f_6"><span class="glyphicon glyphicon-search"></span></p>
                    <p class="f_2">图书<br/>查询</p>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="list-item bg-green center-block" onclick="location.href='{{route('myorder')}}'">
                    <p class="f_6"><span class="glyphicon glyphicon glyphicon-list-alt"></span></p>
                    <p class="f_2">我的<br/>预定</p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
@endsection
