<!DOCTYPE html>
<html class="x-admin-sm">
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
        <div class="layui-fluid">
            <div class="layui-row">
                <form class="layui-form">
                    <input type="hidden" name="uid" value="{{ $user->user_id }}">
                    <div class="layui-form-item">
                        <label for="L_email" class="layui-form-label">
                            <span class="x-red">*</span>邮箱</label>
                        <div class="layui-input-inline">
                            <input type="text" value="{{$user->email}}" id="L_email" name="email" required="" lay-verify="email" autocomplete="off" class="layui-input"></div>
                        <div class="layui-form-mid layui-word-aux">
                            <span class="x-red">*</span>将会成为您唯一的登入名</div></div>
                    <div class="layui-form-item">
                        <label for="L_username" class="layui-form-label">
                            <span class="x-red">*</span>昵称</label>
                        <div class="layui-input-inline">
                            <input type="text" value="{{$user->user_name}}" id="L_username" name="username" required="" lay-verify="nikename" autocomplete="off" class="layui-input"></div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"><span class="x-red">*</span>角色</label>
                        <div class="layui-input-block">
                            @foreach($role as $item)
                                <input type="checkbox" value="{{$item->id}}" name="role_id[]" lay-skin="primary" title="{{$item->role_name}}" @if(in_array($item->id,$own_role)) checked @endif>
                                <div class="layui-unselect layui-form-checkbox" lay-skin="primary">
                                    <span>{{$item->role_name}}</span>
                                </div>
                            @endforeach

                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label for="L_repass" class="layui-form-label"></label>
                        <button class="layui-btn" lay-filter="edit" lay-submit="">增加</button></div>
                </form>
            </div>
        </div>
        <script>layui.use(['form', 'layer'],
            function() {
                $ = layui.jquery;
                var form = layui.form,
                layer = layui.layer;

                //自定义验证规则
                form.verify({
                    nikename: function(value) {
                        if (value.length < 5) {
                            return '昵称至少得5个字符啊';
                        }
                    },
                    pass: [/(.+){6,12}$/, '密码必须6到12位'],
                    repass: function(value) {
                        if ($('#L_pass').val() != $('#L_repass').val()) {
                            return '两次密码不一致';
                        }
                    }
                });

                //监听提交
                form.on('submit(edit)',
                function(data) {
                    var uid = $('input[name="uid"]').val();
                    $.ajax({
                        url:'/admin/user/'+uid,
                        type:'put',
                        dataType:'json',
                        data:data.field,
                        headers:{
                            'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                        },
                        success:function (data) {
                            if(data.code){
                                layer.msg(data.msg,{icon:6},function(){
                                    parent.location.reload(true);
                                });
                            }else{
                                layer.alert(data.msg,{icon:5});
                            }
                        }
                    });
                    return false;
                });

            });</script>
    </body>

</html>