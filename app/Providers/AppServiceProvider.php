<?php

namespace App\Providers;

use App\Model\Cate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
//        $cate    = Cate::get();
//        $cateone = [];
//        $catetwo = [];
//        foreach ($cate as $key => $value) {
//            if ($value->cate_pid == 0) {
//                $cateone[$key] = $value;
//                foreach ($cate as $k => $v) {
//                    if ($value->cate_id == $v->cate_pid) {
//                        $catetwo[$key][$k] = $v;
//                    }
//                }
//            }
//        }
//        view()->share('cateone',$cateone);
//        view()->share('catetwo',$catetwo);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
