@extends('home._main')

@section('title','我的预定')

@section('body')
    @if(!$orders->isEmpty())
        <div class="container-fluid">
            <div class="panel panel-danger order-lists">
                <div class="panel-body" id="scroll">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>书号</th>
                            <th>书名</th>
                            <th>作者</th>
                            <th>单价</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>{{$loop->index+1}}</td>
                            <td>{{$order->number}}</td>
                            <td>{{$order->name}}</td>
                            <td>{{$order->author}}</td>
                            <td>{{$order->price}}</td>
                            <td>
                                <button onclick="cancel_book({{$order->id}},this)" class="btn btn-success">删 除</button>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">
                    <form id="form-order" action="{{route('myorder.store')}}" method="post" onsubmit="return false">
                        {{csrf_field()}}
                    <div class="row">
                        <div class="col-md-3">
                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @break
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="person_id" class="form-control" placeholder="您的编号" autocomplete="off">
                        </div>
                        <div class="col-md-2">
                            <input type="text"  name="person_name"  class="form-control" placeholder="您的姓名" autocomplete="off">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary">立即预定</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="container-fluid">
            <img class="center-block" src="{{asset('images/noresult.gif')}}" alt="" height="400px">
            <div class="text-center h3">
                空空如也！
            </div>
        </div>
    @endif
@endsection

@section('js')
    <script>
        $('.nav-my-order').addClass('active');
        $('#navbar-form').remove();
    </script>
    @if(!$orders->isEmpty())
    <script>
        autoHeight();
        $().ready(function () {
            $(window).resize(function () {
                autoHeight();
            });
        })

        function autoHeight() {
            var top_h = $('.navbar').outerHeight(true);
            var body_h = $(window).height();
            var res_h = body_h - top_h - 25 - 57;
            $('.panel-body').css('height', res_h + 'px');
        }
    </script>
    <script>
        $().ready(function(){
            $('#form-order button').on('click',function(){
                var id = $.trim($('input[name="person_id"]').val());
                var name = $.trim($('input[name="person_name"]').val());
                if(id==''){
                    layer.msg('您的编号不能为空！');
                    return false;
                }
                if(name==''){
                    layer.msg('您的姓名不能为空！');
                    return false;
                }
                if(id && name){
                    $('#form-order').attr('onsubmit','return true');
                    $('#form-order').submit();
                }
            })
        })
        function cancel_book(id,tt){
            layer.confirm('确定删除这本书？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.get("{{url('delete')}}"+'/'+id,{},function(res){
                    layer.msg('删除成功！', {time: 2000, icon:1});
                    $(tt).parent().parent().remove();
                    if(res.message==0){
                        $('.nav-my-order span').addClass('hidden')
                    }
                    $('.nav-my-order span').text(res.message);
                });
            }, function(){});
        }
    </script>
    @endif
@endsection