<?php

namespace App\Http\Controllers\Admin;

use App\Model\Permission;
use App\Model\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $role = Role::orderBy('id', 'asc')
            ->where(function ($query) use ($request) {
                $role_name = $request->input('role_name');
                if (!empty($role_name)) {
                    $query->where('role_name', 'like', '%' . $role_name . '%');
                }
            })->paginate(3);
        return view('admin.role.list', compact('request', 'role'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $permission = Permission::all();
        return view('admin.role.add', compact('permission'));
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
            $input   = $request->all();
            $per_ids = $input['per_id'];
            $role    = Role::create(['role_name' => $input['role_name'], 'desc' => $input['desc']]);
            if (!empty($per_ids)) {
                foreach ($per_ids as $item) {
                    DB::table('role_permission')
                        ->insert(['role_id' => $role->id, 'permission_id' => $item]);
                }
            }
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
        $role       = Role::find($id);
        $permission = Permission::all();
        $own_per    = $role->permission;
        $per_ids    = [];
        foreach ($own_per as $item) {
            $per_ids[] = $item->id;
        }
        return view('admin.role.edit', compact('role', 'permission', 'per_ids'));
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
            $input           = $request->except('_token');
            $role            = Role::find($id);
            $role->role_name = $input['role_name'];
            $role->desc      = $input['desc'];
            $role->save();
            DB::table('role_permission')->where('role_id', $input['role_id'])->delete();
            if (!empty($input['per_id'])) {
                foreach ($input['per_id'] as $item) {
                    DB::table('role_permission')
                        ->insert(['role_id' => $input['role_id'], 'permission_id' => $item]);
                }
            }
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
            $role = Role::find($id);
            $role->delete();
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
            Role::destroy($input);
            return ['code'=>1,'msg'=>'删除成功'];
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
}
