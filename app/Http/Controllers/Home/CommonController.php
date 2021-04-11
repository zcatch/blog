<?php

namespace App\Http\Controllers\Home;

use App\Model\Cate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommonController extends Controller
{
    //
    public function __construct()
    {
        $cate    = Cate::get();
        $cateone = [];
        $catetwo = [];
        foreach ($cate as $key => $value) {
            if ($value->cate_pid == 0) {
                $cateone[$key] = $value;
                foreach ($cate as $k => $v) {
                    if ($value->cate_id == $v->cate_pid) {
                        $catetwo[$key][$k] = $v;
                    }
                }
            }
        }
        view()->share('cateone',$cateone);
        view()->share('catetwo',$catetwo);
    }
}
