@extends('home._main')

@section('title','图书搜索')

@section('body')
    @if(!$books->isEmpty())
        <div class="container-fluid">
            <div class="search-result">
                <div class="row">
                    <div class="col-md-4">
                        <div class="result-lists" id="scroll">
                            <div class="list-group">
                                @foreach($books as $book)
                                <a id="book_id_{{$book->id}}" onclick="bookDetail(this)" class="list-group-item @if($loop->first) active first_book @endif " />
                                    <h4 class="list-group-item-heading">{{$book->name}}</h4>
                                    <span class="item-order">@if($book->add==1) 已预订 @endif</span>
                                    <p class="list-group-item-text">作者：{{$book->author}}</p>
                                    <input type="hidden" name="add" value="{{$book->add}}">
                                    <input type="hidden" name="image" value="{{$book->image}}">
                                    <input type="hidden" name="author" value="{{$book->author}}">
                                    <input type="hidden" name="name" value="{{$book->name}}">
                                    <input type="hidden" name="number" value="{{$book->number}}">
                                    <input type="hidden" name="price" value="{{$book->price}}">
                                    <input type="hidden" name="year" value="{{$book->publish_year}}">
                                    <input type="hidden" name="type" value="{{$book->publish_type}}">
                                    <input type="hidden" name="id" value="{{$book->id}}">
                                </a>
                                @endforeach
                                @if(!empty($books->links()))
                                    <div class="text-center">
                                        {{ $books->appends(['s' => $search])->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="panel panel-primary o_f_h">
                            <div class="result-detail">
                                <div class="panel-body">
                                    <img id="d_image" class="center-block" src="" alt="图片"
                                         height="300px">
                                    <div id="d_btn">
                                    </div>
                                </div>
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>书名：</label><span id="d_name"></span>
                                            </div>
                                            <div class="col-md-4">
                                                <label>书号：</label><span id="d_number"></span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <label>单价：</label><span class="f_2 text-primary" id="d_price"></span>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>作者：</label><span id="d_author"></span>
                                            </div>
                                            <div class="col-md-4">
                                                <label>出版年月：</label><span id="d_publish_year"></span>
                                            </div>
                                            <div class="col-md-4">
                                                <label>版别：</label><span id="d_publish_type"></span>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="container-fluid">
            <img class="center-block" src="{{asset('images/noresult.gif')}}" alt="" height="400px">
            <div class="text-center h3">
                暂无结果！
            </div>
        </div>
    @endif
</body>
@endsection

@section('js')
    <script>
        $('.nav-book-search').addClass('active');
        $('#navbar-form input').prop('placeholder', '书号/书名/作者');
    </script>

    @if(!$books->isEmpty($books))
    <script>
        autoHeight();
        $().ready(function () {
            $('#navbar-btn').on('click', function () {
                var search = $.trim($('#navbar-form input').val());
                $('#navbar-form').attr('action', "{{route('search')}}");
                $('#navbar-form').attr('onsubmit', "return true");
                $('#navbar-form').submit();
            });
            $('.result-lists a').on('click', function () {
                $(this).addClass('active').siblings().removeClass('active');
            })
            $(window).resize(function () {
                autoHeight();
            });
        })

        function autoHeight() {
            var top_h = $('.navbar').outerHeight(true);
            var body_h = $(window).height();
            var res_h = body_h - top_h - 25;
            $('.result-lists').css('height', res_h + 'px');
            $('.result-detail').css('height', res_h + 'px');
            var d_w = $('.result-detail').parent().width();
            $('.result-detail').css('width', d_w + 17 + 'px');
        }
    </script>

    <script>
        $('.first_book').click()
        function bookDetail(tt){
            var image = $(tt).find('input[name="image"]').val();
            var name = $(tt).find('input[name="name"]').val();
            var number = $(tt).find('input[name="number"]').val();
            var price = $(tt).find('input[name="price"]').val();
            var author = $(tt).find('input[name="author"]').val();
            var year = $(tt).find('input[name="year"]').val();
            var type = $(tt).find('input[name="type"]').val();
            var id = $(tt).find('input[name="id"]').val();

            $('.result-detail #d_image').attr('src',"{{asset('images')}}"+'/'+image);
            $('.result-detail #d_name').text(name);
            $('.result-detail #d_number').text(number);
            $('.result-detail #d_price').text(price);
            $('.result-detail #d_author').text(author);
            $('.result-detail #d_publish_year').text(year);
            $('.result-detail #d_publish_type').text(type);

            var add = parseInt($(tt).find('input[name="add"]').val());
            if(add===0){
                var str = '<button onclick="order_book('+id+')" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span>\n' +
                    '                                            预定该书\n' +
                    '                                        </button>';
                $('.result-detail #d_btn').html(str);
            }else{
                var str = '<button onclick="cancel_book('+id+')" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span>\n' +
                    '                                            取消预定\n' +
                    '                                        </button>';
                $('.result-detail #d_btn').html(str);
            }
        }
        function order_book(id){
            $.get("{{url('add')}}"+'/'+id,{},function(res){
                layer.msg('预定成功！', {time: 2000, icon:1});
                $('#book_id_'+id+' .item-order').text('已预订');
                $('#book_id_'+id+' input[name="add"]').val(1);
                var str = '<button onclick="cancel_book('+id+')" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span>\n' +
                    '                                            取消预定\n' +
                    '                                        </button>';
                $('.result-detail #d_btn').html(str);
                $('.nav-my-order span').removeClass('hidden');
                $('.nav-my-order span').text(res.message);
            });
        }
        function cancel_book(id){
            $.get("{{url('delete')}}"+'/'+id,{},function(res){
                layer.msg('取消成功！', {time: 2000, icon:1});
                $('#book_id_'+id+' .item-order').text('')
                $('#book_id_'+id+' input[name="add"]').val(0);
                var str = '<button onclick="order_book('+id+')" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span>\n' +
                    '                                            预定该书\n' +
                    '                                        </button>';
                $('.result-detail #d_btn').html(str);
                if(res.message==0){
                    $('.nav-my-order span').addClass('hidden')
                }
                $('.nav-my-order span').text(res.message);
            });
        }
    </script>
    @endif
@endsection