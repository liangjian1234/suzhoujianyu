@extends('common._base')

@section('base_title')
    @yield('title')
@endsection

@section('base_body')
    <body class="o_f_h">
    <nav class="navbar navbar-inverse" id="admin-navbar">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/"><span class="glyphicon glyphicon-home text-primary"></span></a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="nav-admin-import"><a href="{{route('search')}}">图书入库</a></li>
                    <li class="nav-admin-export"><a href="{{route('myorder')}}">预定导出 <span class="badge bg-red @if(count(session('order_lists'))==0) hidden @endif">{{count(session('order_lists'))}}</span></a></li>
                    <li class="nav-order-record"><a href="{{route('myrecord')}}">预定记录</a></li>
                </ul>
                <form class="navbar-form navbar-left" method="get" id="navbar-form" onsubmit="return false;">
                    <div class="form-group">
                        <input type="text"  name="s" class="form-control" value="" autocomplete="off">
                    </div>
                    <button type="submit" class="btn btn-default" id="navbar-btn">搜 索</button>
                </form>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
    @yield('body')
    </body>
@endsection
@section('base_js')
    <script type="text/javascript" src="{{asset('js/layer.js')}}"></script>
    <script>
        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            }
        })
    </script>
    @yield('js')
@endsection