<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    //
    public $table = 'article';
    public $primaryKey = 'art_id';
    public $timestamps = false;
    public $guarded = [];

    public function cate()
    {
        return $this->belongsTo('App\Model\Cate','cate_id','cate_id');
    }
}
