@extends('admin._main')

@section('title','图书入库')

@section('head')
    <link rel="stylesheet" href="{{asset('datepick/css/bootstrap-datetimepicker.min.css')}}">
@endsection

@section('body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-primary">
                    <div class="panel-body exprot_panel">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-3">
                                        预定开始时间：
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" type="text" id="order_start_date" readonly>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-3">
                                        预定结束时间：
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" type="text" id="order_end_date" readonly>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-3">
                                        导出方式：
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" name="export_type" id="export_type">
                                            <option value="1">按预定记录</option>
                                            <option value="2">按明细</option>
                                        </select>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-3">
                                        <button class="btn btn-primary" id="btn_export">导 出</button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <h3 class="panel-title">最近导出记录</h3>
                    </div>
                    <div class="panel-body recent-record" id="scroll">
                        @if(!$export_record->isEmpty())
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>导出时间</th>
                                    <th>预定开始时间</th>
                                    <th>预定结束时间</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($export_record as $record)
                                    <tr>
                                        <td>{{$record->created_at}}</td>
                                        <td>{{$record->start_date}}</td>
                                        <td>{{$record->end_date}}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        @else
                            <P>暂无</P>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{asset('datepick/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('datepick/js/locales/bootstrap-datetimepicker.zh-CN.js')}}"></script>
    <script>

        $('.nav-admin-export').addClass('active');
        $('#order_start_date').datetimepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,//自动关闭
            minView: 2,//最精准的时间选择为日期0-分 1-时 2-日 3-月
            weekStart: 0,
            language: 'zh-CN'
        }).on('changeDate', function (ev) {
            var starttime = $("#order_start_date").val();
            $("#order_end_date").datetimepicker('setStartDate', starttime);
        });
        $('#order_end_date').datetimepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,//自动关闭
            minView: 2,//最精准的时间选择为日期0-分 1-时 2-日 3-月
            weekStart: 0,
            language: 'zh-CN'
        }).on('changeDate', function (ev) {
            var endtime = $("#order_end_date").val();
            $("#order_start_date").datetimepicker('setEndDate', endtime);
        });

        autoHeight();

        function autoHeight() {
            var top_h = $('#admin-navbar').outerHeight(true);
            var body_h = $(window).height();
            var res_h = body_h - top_h - 20 - 40;
            $('.recent-record').css('max-height', res_h + 'px');
        }
    </script>

    <script>
        $().ready(function () {
            $('#btn_export').on('click', function () {
                var start_date = $('#order_start_date').val();
                var end_date = $('#order_end_date').val();
                var export_type = $('#export_type').val();

                if (start_date == '' || end_date == '' || export_type == '') {
                    layer.msg('请完整填写数据！');
                    return false;
                } else {
                    var data = {start_date: start_date, end_date: end_date, export_type: export_type};
                    var index = layer.load(0, {
                        shade: [0.5, '#000'] //0.1透明度的白色背景
                    });
                    $.post("{{route('export')}}", data, function (res) {
                        layer.close(index);
                        if (res.code === 10000) {
                            location.href = "{{route('export.download')}}" + "?name=" + res.message.name + "&ext=" + res.message.ext;
                        }else{
                            layer.msg(res.message);
                        }
                    })
                }
            })
        })
    </script>

@endsection