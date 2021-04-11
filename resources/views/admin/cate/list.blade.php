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
        <div class="x-nav">
            <span class="layui-breadcrumb">
                <a href="">首页</a>
                <a href="">分类管理</a>
                <a>
                    <cite>文章分类</cite></a>
            </span>
            <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" onclick="location.reload()" title="刷新">
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>
            </a>
        </div>
        <div class="layui-fluid">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <div class="layui-card">
                        <div class="layui-card-body ">
                            <form class="layui-form layui-col-space5" method="get" action="{{url('admin/cate')}}">
                                <div class="layui-input-inline layui-show-xs-block">
                                    <input class="layui-input" value="{{$request->input('cate_name')}}" placeholder="分类名" name="cate_name"></div>
                                <div class="layui-inline layui-show-xs-block">
                                    <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i></button>
                                </div>
                            </form>
                            <hr>
                            <blockquote class="layui-elem-quote">每个tr 上有两个属性 cate-id='1' 当前分类id fid='0' 父级id ,顶级分类为 0，有子分类的前面加收缩图标<i class="layui-icon x-show" status='true'>&#xe623;</i></blockquote>
                        </div>
                        <div class="layui-card-header">
                            <button class="layui-btn layui-btn-danger" onclick="delAll()">
                                <i class="layui-icon"></i>批量删除</button>
                            <button class="layui-btn" onclick="xadmin.open('添加分类','{{url('admin/cate/create')}}',600,400)"><i class="layui-icon"></i>添加</button>
                        </div>
                        <div class="">
                            <table class="layui-table">
                                <thead>
                                <tr>
                                    <th>
                                        <div class="layui-unselect header layui-form-checkbox" lay-skin="primary"><i class="layui-icon">&#xe605;</i></div>
                                    </th>
                                    <th>ID</th>
                                    <th>分类名称</th>
                                    <th>分类标题</th>

                                    <th>操作</th></tr>
                                </thead>
                                <tbody>
                                @foreach($cate as $v)
                                    <tr>
                                        <td>
                                            <div class="layui-input-inline" style="width:35px;">
                                                <input onchange="changeOrder(this,{{ $v->cate_id }})" type="text" name="cate_order" value="{{ $v->cate_order }}" class="layui-input">
                                            </div>

                                        </td>
                                        <td>{{ $v->cate_id }}</td>
                                        <td>{{ $v->cate_name }}</td>
                                        <td>{{ $v->cate_title }}</td>

                                        <td class="td-manage">

                                            <a title="编辑"  onclick="xadmin.open('编辑','{{ url('admin/cate/'.$v->cate_id.'/edit') }}',600,400)" href="javascript:;">
                                                <i class="layui-icon">&#xe642;</i>
                                            </a>

                                            <a title="删除" onclick="member_del(this,{{ $v->cate_id }})" href="javascript:;">
                                                <i class="layui-icon">&#xe640;</i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
          layui.use(['form'], function(){
            form = layui.form;
            
          });
          function changeOrder(obj,id){

              // 获取当前文本框的值（修改后的排序值）
              var order_id = $(obj).val();

              $.post('/admin/cate/changeorder',{'_token':"{{csrf_token()}}","cate_id":id,"cate_order":order_id},function(data){
                  if(data.status == 0){
                      layer.msg(data.msg,{icon:6},function(){
                          location.reload();
                      });
                  }else{
                      layer.msg(data.msg,{icon:5});
                  }
              });
          }
           /*用户-删除*/
          function member_del(obj,id){
              layer.confirm('确认要删除吗？',function(index){
                  $.ajax({
                      type:'delete',
                      dataType:'json',
                      url:'/admin/cate/'+id,
                      headers:{
                          'X-CSRF-TOKEN':'{{csrf_token()}}',
                      },
                      success:function (data) {
                          if(data.code){
                              layer.msg(data.msg,{icon:6},function(){
                                  parent.location.reload(true);
                              });
                          }else{
                              layer.msg(data.msg,{icon:5});
                          }
                      }
                  });
                  return false;
              });
          }

          // 分类展开收起的分类的逻辑
          // 
          $(function(){
            $("tbody.x-cate tr[fid!='0']").hide();
            // 栏目多级显示效果
            $('.x-show').click(function () {
                if($(this).attr('status')=='true'){
                    $(this).html('&#xe625;'); 
                    $(this).attr('status','false');
                    cateId = $(this).parents('tr').attr('cate-id');
                    $("tbody tr[fid="+cateId+"]").show();
               }else{
                    cateIds = [];
                    $(this).html('&#xe623;');
                    $(this).attr('status','true');
                    cateId = $(this).parents('tr').attr('cate-id');
                    getCateId(cateId);
                    for (var i in cateIds) {
                        $("tbody tr[cate-id="+cateIds[i]+"]").hide().find('.x-show').html('&#xe623;').attr('status','true');
                    }
               }
            })
          })

          var cateIds = [];
          function getCateId(cateId) {
              $("tbody tr[fid="+cateId+"]").each(function(index, el) {
                  id = $(el).attr('cate-id');
                  cateIds.push(id);
                  getCateId(id);
              });
          }
   
        </script>
    </body>
</html>
