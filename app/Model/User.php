<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    //
    protected $table = 'user';
    public $primaryKey = 'user_id';
    public $timestamps = false;
    //    public $fillable = ['user_name','user_pass','email','phone'];
    public $guarded = [];

    public function role()
    {
        return $this->belongsToMany('App\Model\Role','user_role','user_id','role_id');
    }
}
