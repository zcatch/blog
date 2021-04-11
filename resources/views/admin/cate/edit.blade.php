<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>欢迎页面-X-admin2.2</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.public.styles')
    @include('admin.public.script')
</head>

<body>
<div class="x-body">
    <form class="layui-form">
        <input name="cate_id" type="hidden" value="{{$cate->cate_id}}">
        <div class="layui-form-item">
            <label for="L_email" class="layui-form-label">
                <span class="x-red">*</span>父级分类
            </label>
            <div class="layui-input-inline">
                <select name="cate_pid">
                    <option value="0">==顶级分类==</option>
                    @foreach($cates as $v)
                        <option value="{{ $v->cate_id }}" @if($cate->cate_id == $v->cate_id) selected @endif >{{ $v->cate_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="layui-form-mid layui-word-aux">
                <span class="x-red">*</span>
            </div>
        </div>

        <div class="layui-form-item">
            <label for="L_username" class="layui-form-label">
                <span class="x-red">*</span>分类名称
            </label>
            <div class="layui-input-inline">
                <input type="text" value="{{$cate->cate_name}}" id="L_username" name="cate_name" required=""
                        autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="L_catetitle" class="layui-form-label">
                <span class="x-red">*</span>分类标题
            </label>
            <div class="layui-input-inline">
                <input type="text" value="{{$cate->cate_title}}" id="L_catetitle" name="cate_title" required=""
                        autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="L_cate_order" class="layui-form-label">
                <span class="x-red">*</span>排序
            </label>
            <div class="layui-input-inline">
                <input type="text" value="{{$cate->cate_order}}" id="L_cate_order" name="cate_order" required=""
                        autocomplete="off" class="layui-input">

            </div>
        </div>
        <div class="layui-form-item">
            <label for="L_repass" class="layui-form-label">
            </label>
            <button  class="layui-btn" lay-filter="edit" lay-submit="">
                修改
            </button>
        </div>
    </form>
</div>
<script>
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form
            ,layer = layui.layer;
        //监听提交
        form.on('submit(edit)', function(data){
            var cateid = $("input[name='cate_id']").val();
            //console.log(uid);
            $.ajax({
                type: 'PUT',
                url: '/admin/cate/'+cateid,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // data:JSON.stringify(data.field),
                data:data.field,
                success: function(data){

                    if(data.code){
                        layer.msg(data.msg, {icon: 6},function () {
                            parent.location.reload();
                        });
                    }else{
                        layer.alert(data.msg, {icon: 5});
                    }

                },
            });
            return false;
        });


    });
</script>
</body>

</html>