<?php

namespace App\Http\Controllers\Admin;

use App\Model\Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{

    //将网站配置数据表中的网站配置数据写入config/webconfig.php文件中
    public function putContent()
    {
//        1. 从网站配置表中获取网站配置数据
        $content = Config::pluck('conf_content', 'conf_name')->all();
//        dd($content);
//        2. 准备要写入的内容

        $str = '<?php return ' . var_export($content, true) . ';';

//        3. 将内容写入webconfig.php文件

        file_put_contents(config_path() . '/webconfig.php', $str);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $conf = Config::get();
        foreach ($conf as $v) {
            switch ($v->field_type) {
                //1. 文本框 input
                case 'input':
                    $v->conf_contents = '<input value="' . $v->conf_content . '" type="text" name="conf_content[]"  class="layui-input">';
                    break;
                //2 文本域 textarea
                case 'textarea':
                    $v->conf_contents = '<textarea name="conf_content[]" class="layui-textarea">' . $v->conf_content . '</textarea>';
                    break;
                //3 单选按钮 raido
                case 'radio':
//                    定义一个字符串，存放最终的拼接结果
                    $str = '';
                    $arr = explode(',', $v->field_value);
                    foreach ($arr as $n) {
                        $a = explode('|', $n);
                        if ($a[0] == $v->conf_content) {
                            $str .= '<input type="radio" checked name="conf_content[]" value="' . $a[0] . '" title="' . $a[1] . '">&nbsp;&nbsp;&nbsp;';
                        } else {
                            $str .= '<input type="radio"  name="conf_content[]" value="' . $a[0] . '" title="' . $a[1] . '">&nbsp;&nbsp;&nbsp;';
                        }
                    }
                    $v->conf_contents = $str;
                    break;
            }
        }
        return view('admin.config.list', compact('conf'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.config.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $input = $request->except('_token', 'admin/config');
        $res   = Config::create($input);
        if ($res) {
//            $this->putContent();
            return redirect('admin/config');
        } else {
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = Config::find($id)->delete();
        //如果删除成功
        if ($res) {
            $this->putContent();
            $data = [
                'status'  => 0,
                'message' => '删除成功',
            ];
        } else {
            $data = [
                'status'  => 1,
                'message' => '删除失败',
            ];
        }

        return $data;
    }

    //批量修改网站配置项的方法
    public function changeContent(Request $request)
    {
        $input = $request->except('_token');
        DB::beginTransaction();
        try {
            $conf_content = array_values($input['conf_content']);
            foreach ($input['conf_id'] as $k => $v) {
                DB::table('config')->where('conf_id', $v)->update(['conf_content' => $conf_content[$k]]);
            }
            DB::commit();
            $this->putContent();
            return redirect('/admin/config');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
