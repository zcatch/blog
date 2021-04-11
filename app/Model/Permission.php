<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    //
    protected $table = 'permission';
    public $primaryKey = 'id';
    public $timestamps = false;
    public $guarded = [];
}
