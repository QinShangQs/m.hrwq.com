<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Route;
use App,Log;
use App\Models\User;
class FrontWechat
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   //已登录
        if(session('user_info')){
            return $next($request);
        }
        
        //未登录
    	$lover_id = 0;
    	$lover_time = null;
        //爱心大使
    	if(preg_match('/share\/hot\/(\d+)/i', $request->path(), $out)){
    		$lover_id = $out[1];
    		$lover_time = date('Y-m-d H:i:s');
    	}
        
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            if(config('app.debug') === true && config('app.env') === 'dev'
                    && strpos($user_agent, 'MicroMessenger') === false){
                $user_agent = "MicroMessenger";
                //方便测试临时制造数据 TODO 
                $wechatUsertemp = [
                         'openid' => 'obpqNs_GdrHPLOGJig50qNcFZRGk',// 
                         'nickname' => '秦殇2607',
                         'sex' => '1',
                         'city' => '泰州',
                         'province' => '江苏',
                         'headimgurl' => 'http://m.hrwq.com//uploads/profileIcon/20180626/ot3XZt_Zq7p5PPFuihUQgY6Nn9fY.jpg',
                ];
                $request->session()->put('wechat_user', $wechatUsertemp);
            }
            
            if (strpos($user_agent, 'MicroMessenger') !== false ) {             
                if (empty($request->session()->get('wechat_user')) ) {
                    return redirect('/wechat/auth?url='.$request->getRequestUri());
                }
                
                $wechat_user = $request->session()->get('wechat_user');
                if(!isset($wechat_user['openid'])){
                    $request->session()->forget('wechat_user');
                    return redirect('/wechat/auth?url='.$request->getRequestUri());
                }
                
                $user_info = User::whereOpenid($wechat_user['openid'])->first();
                if ($user_info && $user_info['id'] > 0 && $user_info['block']=='1') {

                    if ($request->session()->get('user_info')) {
                    	return $next($request);
                    }
                    
                    User::whereOpenid($wechat_user['openid'])->update(['last_login' => date('Y-m-d H:i:s', time())]);
                    $request->session()->set('user_info', $user_info->toArray());
                } elseif ($user_info && $user_info['id'] > 0 && $user_info['block']=='2') {
                    return redirect(route('course.block_index'));
                } else {
                    $request->session()->forget('user_info');
                    //首次后台绑定用户
                    $userModel = new User();
                    $save_path = 'uploads/profileIcon/'.date('Ymd').'/';
                    $save_name = $wechat_user['openid'].'.jpg';
                    if (!file_exists($save_path)) {
                        $this->createFolder($save_path);
                    }
                    $content = curl_file_get_contents($wechat_user['headimgurl']);
                    file_put_contents($save_path.$save_name, $content);
                    $profileIcon = $save_path.$save_name;

                    if (!is_file($profileIcon)) {
                        $profileIcon = $wechat_user['headimgurl'];
                    }
                    $user_exist = User::where('openid',$wechat_user['openid'])->first();
                    if ($user_exist) {
                        return $next($request);
                    } else {
                        $userModel->openid = $wechat_user['openid'];
                        $userModel->nickname = $wechat_user['nickname'];
                        $userModel->profileIcon = '/'.$profileIcon;
                        $userModel->sex = $wechat_user['sex'];
                        
                        $userModel->lover_id = $lover_id;
                        $userModel->lover_time = $lover_time;
                        if($userModel->save()){
                            $request->session()->set('user_info', $userModel->toArray());
                            return $next($request);
                        }else{
                            return $next($request);
                        }
                    }
                }
            } 
            else {
                //非微信浏览器
                return response()->redirectTo('wechat/qrcode');
            }
        } else {
            header("Content-type: text/html; charset=utf-8");
            echo '本站禁止被采集！';
            exit;
        }
        return $next($request);
    }

    /*创建文件夹*/
    private function createFolder($path = null, $mode = 0777)
    {
        $dirs = explode('/', $path);
        $dir = '';
        foreach ($dirs as $part) {
            $dir .= $part . '/';
            if (!is_dir($dir) && strlen($dir) > 0) {
                mkdir($dir, $mode);
            }
        }
    }
}
