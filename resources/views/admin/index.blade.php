@extends('admin._main')

@section('title','图书入库')

@section('head')
    <link rel="stylesheet" href="{{asset('fileinput/css/fileinput.min.css')}}">
@endsection

@section('body')
    <div class="container-fluid">
        <div class="row field-merge hidden" >
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-danger">
                    <!-- Default panel contents -->
                    <div class="panel-heading">准备入库</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                请选择入库方式：
                            </div>
                            <div class="col-md-4">
                                <select name="import_type" class="form-control">
                                    <option value="1">与原有数据合并</option>
                                    <option value="2">覆盖原有数据</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary">确 认</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row file-import">
            <div class="col-md-6 col-md-offset-3">
                <input name="import" type="file" id="import-excel">
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script type="application/javascript" src="{{asset('fileinput/js/fileinput.min.js')}}"></script>
    <script type="application/javascript" src="{{asset('fileinput/js/locales/zh.js')}}"></script>
    <script>
        var name = '';
        var ext = '';

        $('.nav-admin-import').addClass('active');

        $("#import-excel").fileinput({
            showCaption: true,
            theme: 'fa',
            language: 'zh',
            maxFileCount: 1,
            showPreview: true,
            maxFileSize: 0,
            allowedFileExtensions: ['xls', 'xlsx'],
            uploadUrl: "{{route('import')}}"
        }).on("fileuploaded", function (event, data) {
            console.log(data)
            if (data.response) {
                $('.field-merge').removeClass('hidden');
                name = data.response.message.name;
                ext = data.response.message.extension;
            }
        });

        $('.field-merge button').on('click',function(){
            var type = $('select[name="import_type"]').val();
            if(name=='' || ext=='' || type==''){
                layer.msg('上传文件出错！',{icon:2,time:3000});
                return false;
            }
            if(type==2){
                layer.confirm('此操作会删除数据库原有数据，是否继续？', {
                    btn: ['继续','取消'] //按钮
                }, function(){
                    var data = {type:type,name:name,extension:ext};
                    var index = layer.load(0, {
                        shade: [0.5,'#000'] //0.1透明度的白色背景
                    });
                    $.get("{{route('import.store')}}",data,function(res){
                        if(res.code===10000){
                            layer.alert('图书入库成功！', {
                                skin: 'layui-layer-lan' //样式类名
                                ,closeBtn: 0
                            }, function(){
                                history.go(0);
                            });
                        }else{
                            layer.msg(res.message,{icon:2,time:3000});
                        }
                        layer.close(index);
                    })
                }, function(){
                    return false;
                });
            }else{
                var data = {type:type,name:name,extension:ext};
                var index = layer.load(0, {
                    shade: [0.5,'#000'] //0.1透明度的白色背景
                });
                $.get("{{route('import.store')}}",data,function(res){
                    console.log(res);
                    if(res.code===10000){
                        layer.alert('图书入库成功！', {
                            skin: 'layui-layer-lan' //样式类名
                            ,closeBtn: 0
                        }, function(){
                            history.go(0);
                        });
                    }else{
                        layer.msg(res.message,{icon:2,time:3000});
                    }
                    layer.close(index);
                })
            }
        })

    </script>

@endsection