<!DOCTYPE html>
<html class="x-admin-sm">

  <head>
    <meta charset="UTF-8">
    <title>欢迎页面-X-admin2.2</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
      @include('admin.public.styles')
      @include('admin.public.script')
  </head>

  <body>
    <div class="layui-fluid">
        <div class="layui-row">
            <form action="" method="post" class="layui-form layui-form-pane">
                <input type="hidden" name="permission_id" value="{{$permission->id}}">
                <div class="layui-form-item">
                    <label for="name" class="layui-form-label">
                        <span class="x-red">*</span>权限名称
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="{{$permission->per_name}}" id="per_name" name="per_name" required="" lay-verify="required"
                        autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="name" class="layui-form-label">
                        <span class="x-red">*</span>权限规则
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="{{$permission->per_url}}" id="per_url" name="per_url" required="" lay-verify="required"
                                autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                <button class="layui-btn" lay-submit="" lay-filter="edit">保存</button>
              </div>
            </form>
        </div>
    </div>
    <script>
        layui.use(['form','layer'], function(){
            $ = layui.jquery;
          var form = layui.form
          ,layer = layui.layer;

          //自定义验证规则
          form.verify({
            nikename: function(value){
              if(value.length < 5){
                return '昵称至少得5个字符啊';
              }
            }
            ,pass: [/(.+){6,12}$/, '密码必须6到12位']
            ,repass: function(value){
                if($('#L_pass').val()!=$('#L_repass').val()){
                    return '两次密码不一致';
                }
            }
          });

          //监听提交
          form.on('submit(edit)', function(data){
              var permission_id = $('input[name="permission_id"]').val();
            $.ajax({
                type:'put',
                url:'/admin/permission/'+permission_id,
                data:data.field,
                headers:{'X-CSRF-TOKEN':'{{csrf_token()}}'},
                success:function (res) {
                    if(res.code){
                        layer.msg(res.msg,{icon:6},function () {
                            parent.location.reload(true);
                        });
                    }else{
                        layer.alert(res.msg,{icon:5});
                    }
                }
            });
            return false;
          });


        form.on('checkbox(father)', function(data){

            if(data.elem.checked){
                $(data.elem).parent().siblings('td').find('input').prop("checked", true);
                form.render();
            }else{
               $(data.elem).parent().siblings('td').find('input').prop("checked", false);
                form.render();
            }
        });


        });
    </script>
    <script>var _hmt = _hmt || []; (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?b393d153aeb26b46e9431fabaf0f6190";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
      })();</script>
  </body>

</html>