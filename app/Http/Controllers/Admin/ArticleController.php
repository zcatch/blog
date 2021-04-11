<?php

namespace App\Http\Controllers\Admin;

use App\Model\Article;
use App\Model\Cate;
use App\Services\OSS;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Markdown;
use Image;
use Storge;
use Redis;

class ArticleController extends Controller
{
    public function pre_mk(Request $request)
    {
        return Markdown::convertToHtml($request->input('cont'));
    }

    public function upload(Request $request)
    {
        $file = $request->file('photo');
        if (!$file->isValid()) {
            return response()->json(['ServerNo' => '400', 'ResultData' => '无效的上传文件']);
        }
        //获取原文件的扩展名
        $ext = $file->getClientOriginalExtension();    //文件拓展名
        //新文件名
        $newfile = md5(time() . rand(1000, 9999)) . '.' . $ext;

        //文件上传的指定路径
        $path = public_path('uploads');
        //将文件从临时目录移动到本地指定目录
        if (!$file->move($path, $newfile)) {
            return response()->json(['ServerNo' => '400', 'ResultData' => '保存文件失败']);
        }
        return response()->json(['ServerNo' => '200', 'ResultData' => $newfile]);

        //缩放图片
//        $res = Image::make($file)->resize(100,100)->save($path.'/'.$newfile);

        //阿里云对象存储
//        $res = OSS::upload($newfile, $file->getRealPath());
//        $res = \Storage::disk('qiniu')->writeStream($newfile, fopen($file->getRealPath(), 'r'));
//        if (!$res) {
//            return response()->json(['ServerNo' => '400', 'ResultData' => '保存文件失败']);
//        }
//        return response()->json(['ServerNo' => '200', 'ResultData' => $newfile]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $arts = [];
//        $arts = Article::orderBy('art_id', 'asc')->paginate(3);
        $listKey = 'list:article';
        $hashKey = 'hash:article';
        if (Redis::exists($listKey)) {
            $list = Redis::lrange($listKey, 0, -1);
            foreach ($list as $key => $value) {
                $hasData = Redis::hgetall($hashKey . $value);
                if (!empty($hasData)) {
                    $arts[] = $hasData;
                }
            }
        } else {
            $arts = Article::get()->toArray();
            foreach ($arts as $key => $value) {
                //将文章的id添加到listkey变量中
                Redis::rpush($listKey, $value['art_id']);
//                将文章添加到hashkey变量中
                Redis::hmset($hashKey . $value['art_id'], $value);
            }
        }
        return view('admin.article.list', compact('arts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $cates = (new Cate())->tree();
        return view('admin.article.add', compact('cates'));
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
        $listKey = 'list:article';
        $hashKey = 'hash:article';

        $input = $request->except('_token', 'photo', 'admin/article');
        //添加时间
        $input['art_time']   = time();
        $input['art_view']   = 0;
        $input['art_status'] = 0;

        // 将提交的文章数据保存到数据库
        $res = Article::create($input);

        if ($res) {
//            如果添加成功，更新redis
            \Redis::rpush($listKey, $res->art_id);
            \Redis::hMset($hashKey . $res->art_id, $res->toArray());

            return redirect('admin/article');
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
        $arts  = Article::find($id);
        $cates = (new Cate())->tree();
        return view('admin.article.edit', compact('arts', 'cates'));
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
        $input                    = $request->except('artid', '_token', 'phote');
        $article                  = Article::find($id);
        $article->cate_id         = $input['cate_id'];
        $article->art_title       = $input['art_title'];
        $article->art_editor      = $input['art_editor'];
        $article->art_thumb       = $input['art_thumb'];
        $article->art_tag         = $input['art_tag'];
        $article->art_description = $input['art_description'];
        $article->art_content     = $input['art_content'];
        $res                      = $article->save();
        $hashKey                  = 'hash:article';
        \Redis::hMset($hashKey . $id, $input);
        if ($res) {
            $data = [
                'status' => 0,
                'msg'    => '修改成功',
            ];
        } else {
            $data = [
                'status' => 1,
                'msg'    => '修改失败',
            ];
        }
        return $data;
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
        $res = Article::find($id)->delete();
        //如果删除成功
        if ($res) {
            $listKey = 'list:article';
            $hashKey = 'hash:article';
            Redis::lrem($listKey, $id, 0);
            Redis::del($hashKey . $id);
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

    public function recommend(Request $request)
    {
        // 更新添加到推荐位状态
        $input = $request->all();
        $art = Article::find($input['id']);
        if ($input['status'] == 1) {
            $res = $art->update(['art_status' => 0]);
            Redis::hMset('hash:article'.$input['id'],Article::find($input['id'])->toArray());
            if ($res) {
                $data = [
                    'status'  => 0,
                    'message' => '操作成功',
                ];
            } else {
                $data = [
                    'status'  => 1,
                    'message' => '操作失败',
                ];
            }
        } else {
            $res = $art->update(['art_status' => 1]);
            Redis::hMset('hash:article'.$input['id'],Article::find($input['id'])->toArray());
            if ($res) {
                $data = [
                    'status'  => 0,
                    'message' => '操作成功',
                ];
            } else {
                $data = [
                    'status'  => 1,
                    'message' => '操作失败',
                ];
            }
        }
        return $data;
    }
}
