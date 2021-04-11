<?php

namespace App\Http\Controllers\Admin;

use App\Model\Cate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $cate = (new Cate())->tree();
        return view('admin.cate.list', compact('cate', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $cate = Cate::where('cate_pid', 0)->get();
        return view('admin.cate.add', compact('cate'));
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
        $input = $request->except('_token');
        DB::beginTransaction();
        try {
            Cate::create([
                'cate_name'  => $input['cate_name'],
                'cate_pid'   => $input['cate_pid'],
                'cate_order' => $input['cate_order'],
                'cate_title' => $input['cate_title'],
            ]);
            DB::commit();
            return ['code' => 1, 'msg' => '添加成功'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code' => 0, 'msg' => $e->getMessage()];
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
        $cate  = Cate::find($id);
        $cates = Cate::where('cate_pid', 0)->where('cate_id', '<>', $id)->get();
        return view('admin.cate.edit', compact('cates', 'cate'));
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
        $input = $request->input();
        DB::beginTransaction();
        try {
            $cate             = Cate::find($id);
            $cate->cate_name  = $input['cate_name'];
            $cate->cate_pid   = $input['cate_pid'];
            $cate->cate_order = $input['cate_order'];
            $cate->cate_title = $input['cate_title'];
            $cate->save();
            DB::commit();
            return ['code' => 1, 'msg' => '保存成功'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
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
        //
        DB::beginTransaction();
        try {
            Cate::where('cate_id', $id)->delete();
            return ['code' => 1, 'msg' => '删除成功'];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
    //修改排序
    public function changeOrder(Request $request)
    {
//        1. 获取传过来的参数
        $input = $request->except('_token');
        //2. 通过分类id获取当前分类
        $cate = Cate::find($input['cate_id']);
        //3. 修改当前分类的排序值
        $res = $cate->update(['cate_order'=>$input['cate_order']]);

        if($res){
            $data = [
                'status'=>0,
                'msg'=>'修改成功'
            ];
        }else{
            $data = [
                'status'=>1,
                'msg'=>'修改失败'
            ];
        }

        return $data;
    }
}
