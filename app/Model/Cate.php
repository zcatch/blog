<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Cate extends Model
{
    //
    protected $table = 'category';
    public $primaryKey = 'cate_id';
    public $timestamps = false;
    public $guarded = [];

    public function tree()
    {
        $cate = $this->orderBy('cate_order','asc')->get();
        return $this->getTree($cate);
    }

    public function getTree($cate)
    {
        $cates = [];
        foreach ($cate as $value){
            if($value->cate_pid == 0){
                $cates[] = $value;
                foreach ($cate as $v){
                    if($v->cate_pid == $value->cate_id){
                        $cates[] = $v;
                    }
                }
            }
        }
        return $cates;
    }

    public function article()
    {
        return $this->hasMany('App\Model\Article','cate_id','cate_id');
    }
}
