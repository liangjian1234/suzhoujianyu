@extends('home._main')

@section('title','预定记录')

@section('body')
    @if(!$orders->isEmpty())
        <div class="container-fluid" id="scroll">
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                @foreach($orders as $order)
                    <div class="panel panel-info">
                        <div class="panel-heading" role="tab" id="heading{{$loop->index}}">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion"
                                   href="#collapse{{$loop->index}}"
                                   aria-expanded="@if($loop->first) true @else false @endif"
                                   aria-controls="collapse{{$loop->index}}">
                                    <div class="row">
                                        <div class="col-md-1">
                                            # {{$loop->index+1}}
                                        </div>
                                        <div class="col-md-3">
                                            订单号：{{$order->id}}
                                        </div>
                                        <div class="col-md-3">
                                            预定时间：{{$order->created_at}}
                                        </div>
                                        <div class="col-md-3  text-right">
                                            编号：{{$order->person_id}}
                                        </div>
                                        <div class="col-md-2 text-right">
                                            姓名：{{$order->person_name}}
                                        </div>
                                    </div>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse{{$loop->index}}" class="panel-collapse collapse @if($loop->first) in @endif"
                             role="tabpanel" aria-labelledby="heading{{$loop->index}}">
                            <div class="panel-body">
                                <ul class="list-group">
                                    @foreach($order->books as $book)
                                        <li class="list-group-item list-group-item-warning">
                                            <div class="row">
                                                <div class="col-md-1 col-md-offset-1">
                                                    {{$loop->index+1}}
                                                </div>
                                                <div class="col-md-2">
                                                    {{$book->number}}
                                                </div>
                                                <div class="col-md-2">
                                                    {{$book->name}}
                                                </div>
                                                <div class="col-md-2">
                                                    {{$book->author}}
                                                </div>
                                                <div class="col-md-2">
                                                    {{$book->price}}
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @if(!empty($orders->links()))
            <div class="container-fluid text-center" id="pagination">
                <nav aria-label="Page navigation">
                    {{ $orders->appends(['s' => $search])->links() }}
                </nav>
            </div>
        @endif

    @else
        <div class="container-fluid">
            <img class="center-block" src="{{asset('images/noresult.gif')}}" alt="" height="400px">
            <div class="text-center h3">
                暂无记录！
            </div>
        </div>
    @endif
@endsection

@section('js')
    <script>
        $('.nav-order-record').addClass('active');
        $('#navbar-form input').prop('placeholder', '您的编号');
        $('#navbar-btn').on('click', function () {
            var search = $.trim($('#navbar-form input').val());
            $('#navbar-form').attr('action', "{{route('myrecord')}}");
            $('#navbar-form').attr('onsubmit', "return true");
            $('#navbar-form').submit();
        });
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
                var pag_h = $('#pagination').outerHeight(true);
                var body_h = $(window).height();
                var res_h = body_h - top_h - pag_h;
                $('#scroll').css('height', res_h + 'px');
            }
        </script>
    @endif
@endsection