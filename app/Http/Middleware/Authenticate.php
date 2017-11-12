<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate extends Controller
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $data = null)
    {
        $guard = null;
        if ($this->auth->guard($guard)->guest()) {
            if($data == 'arr'){
                return $this->setReturnMessage([], 'error', 'NotOK', 401, 'Unauthorized!', 'User Unauthorized Access!');
            }elseif($data == 'obj'){
                return $this->setReturnMessage((object)[], 'error', 'NotOK', 401, 'Unauthorized!', 'User Unauthorized Access!');
            }else {
                return $this->setReturnMessage((object)[], 'error', 'NotOK', 401, 'Unauthorized!', 'User Unauthorized Access!');
            }
        }

        return $next($request);
    }
}
