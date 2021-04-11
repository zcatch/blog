<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    //
    protected $table = 'config';
    public $primaryKey = 'conf_id';
    public $timestamps = false;
    public $guarded = [];
}
