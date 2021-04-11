<?php

namespace App\Http\Controllers\Admin;

use App\Model\Role;
use App\Model\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $list = User::orderBy('user_id', 'asc')
            ->where(function ($query) use ($request) {
                $username = $request->input('username');
                $email    = $request->input('email');
                if (!empty($username)) {
                    $query->where('user_name', 'like', '%' . $username . '%');
                }
                if (!empty($email)) {
                    $query->where('email', 'like', '%' . $email . '%');
                }
            })
            ->paginate(3);
        return view('admin.user.list', compact('list', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $role = Role::all();
        return view('admin.user.add', compact('role'));
    }

    /**
     * store
     * User  zqs
     * Date  2021/1/27 15:28
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();
        DB::beginTransaction();
        try {
            $pwd     = Crypt::encrypt($input['pass']);
            $rs      = User::create(['user_name' => $input['username'], 'user_pass' => $pwd, 'email' => $input['email']]);
            $user_id = $rs->user_id;
            DB::table('user_role')->where('user_id', $user_id)->delete();
            $post_role_ids = $input['role_id'];
            if (!empty($post_role_ids)) {
                foreach ($post_role_ids as $item) {
                    DB::table('user_role')->insert(['role_id' => $item, 'user_id' => $user_id]);
                }
            }
            if ($rs) {
                return ['code' => 1, 'msg' => '添加成功'];
            } else {
                return ['code' => 0, 'msg' => '添加失败'];
            }
            DB::commit();
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
        $user = User::find($id);
        $role = Role::all();
        $user_role = $user->role;
        $own_role = [];
        foreach ($user_role as $item){
            $own_role[] = $item->id;
        }
        return view('admin.user.edit', compact('user','role','own_role'));
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
            $user            = User::find($id);
            $user->user_name = $request->input('username');
            $user->email     = $request->input('email');
            $user->save();

            DB::table('user_role')->where('user_id', $id)->delete();
            $post_role_ids = $request->input('role_id');
            if (!empty($post_role_ids)) {
                foreach ($post_role_ids as $item) {
                    DB::table('user_role')->insert(['role_id' => $item, 'user_id' => $id]);
                }
            }
            return ['code' => 1, 'msg' => '修改成功'];
            DB::commit();
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
            $user = User::find($id);
            $user->delete();
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
        try {
            $input = $request->input('ids');
            User::destroy($input);
            return ['code' => 1, 'msg' => '删除成功'];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
}
