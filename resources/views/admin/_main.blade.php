@extends('common._base')

@section('base_title')
    @yield('title')
@endsection

@section('base_head')
    @yield('head')
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
                    <li class="nav-admin-import"><a href="{{route('admin')}}">图书入库</a></li>
                    <li class="nav-admin-export"><a href="{{route('export')}}">图书预定导出 </a></li>
                </ul>
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