<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Route;
use Response;
use App\Models\User;
use EasyWeChat\Foundation\Application;

class FrontAuth
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
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        // if (strpos($user_agent, 'MicroMessenger') == false) {
            $user_info = $request->session()->get('user_info');
            if ($user_info) {
                if (User::hasMobile($user_info['id'])) {
                    return $next($request);
                } else {
                    //未验证手机
                    $request->session()->forget('user_info');
                    return redirect('/user/login?url='.$request->getRequestUri());
                }
            } else {
                return redirect('/user/login?url='.$request->getRequestUri());
            }
       // }
       return $next($request);
    }
}
