<?php

namespace App\Http\Controllers\Admin;

use App\Model\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $permission = Permission::orderBy('id', 'asc')
            ->where(function ($query) use ($request) {
                $permission_name = $request->input('permission_name');
                if (!empty($permission_name)) {
                    $query->where('per_name', 'like', '%' . $permission_name . '%');
                }
            })->paginate(3);
        return view('admin.permission.list', compact('permission', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.permission.add');
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
        DB::beginTransaction();
        try {
            $input = $request->all();
            Permission::create(['per_name' => $input['per_name'], 'per_url' => $input['per_url']]);
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
        $permission = Permission::find($id);
        return view('admin.permission.edit', compact('permission'));
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
        DB::beginTransaction();
        try {
            $input         = $request->except('_token');
            $per           = Permission::find($id);
            $per->per_name = $input['per_name'];
            $per->per_url  = $input['per_url'];
            $per->save();
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
            $per = Permission::find($id);
            $per->delete();
            return ['code' => 1, 'msg' => '删除成功'];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
    public function delAll(Request $request)
    {
        DB::beginTransaction();
        try{
            $input = $request->input('ids');
            Permission::destroy($input);
            return ['code'=>1,'msg'=>'删除成功'];
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
}
