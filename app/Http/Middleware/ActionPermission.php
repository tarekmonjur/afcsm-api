<?php
/**
 * Created by PhpStorm.
 * User: Tarek
 * Date: 10/5/2017
 * Time: 1:49 PM
 */

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class ActionPermission
{
    public function handle($request, Closure $next, $guard = null)
    {
        if ($request->header('api-token')){
            $user = User::where('api_token', $request->header('api-token'))->first();
            if($user){
                return $next($request);
            }
        }
        return $this->setReturnMessage([],'error','NotOK',401,'Unauthorized!','User Unauthorized Access!');

    }
}