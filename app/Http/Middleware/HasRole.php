<?php

namespace App\Http\Middleware;

use App\Model\Role;
use App\Model\User;
use Closure;
use Illuminate\Support\Facades\Route;

class HasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = Route::current()->getActionName();
        $user = User::find(session()->get('user')->user_id);
        $roles = $user->role;
        $own_per = [];
        foreach ($roles as $item){
            $pers = $item->permission;
            $r[$item->id][] = $pers;
            if($pers){
                foreach ($pers as $i){
                    $own_per[]=$i->per_url;
                }
            }
        }
        $own_per = array_unique($own_per);
        if(in_array($route,$own_per) or $user->user_name == 'admin'){
            return $next($request);
        }else{
            return redirect('noaccess');
        }
    }
}
