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
    {
    	$lover_id = 0;
    	$lover_time = null;
    	if(preg_match('/share\/hot\/(\d+)/i', $request->path(), $out)){
    		$lover_id = $out[1];
    		$lover_time = date('Y-m-d H:i:s');
    	}
    	
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            if (strpos($user_agent, 'MicroMessenger') !== false ) {
                //方便测试临时制造数据 TODO
                //     $wechatUsertemp = [
                //     'openid' => 'oveRcwX9qpQ-p_EH5IIzLq_wGUpc',
                //     'nickname' => '阿虚',
                //     'sex' => '1',
                //     'city' => '泰州',
                //     'province' => '江苏',
                //     'headimgurl' => 'http://hrwq.test.looip.com/uploads/profileIcon/20160816/AA.jpg',
                // ];
                // $request->session()->put('wechat_user', $wechatUsertemp);
                if (empty($request->session()->get('wechat_user'))) {
                    return redirect('/wechat/auth?url='.$request->getRequestUri());
                }
                
                $wechat_user = $request->session()->get('wechat_user');
                $user_info = User::whereOpenid($wechat_user['openid'])->first();
               
                if ($user_info && $user_info['id'] > 0 && $user_info['block']=='1') {
                    //爱心大使ID不为0，且用户是非会员，不可关联自己
                    if($lover_id != 0 
                    	&& $user_info['vip_flg'] == 1
                    	&& $lover_id != $user_info['id']){
                    	if($user_info['lover_id'] == 0){//没有关联关系，建立关系
                    		User::whereOpenid($wechat_user['openid'])->update(['lover_id'=>$lover_id,'lover_time' => $lover_time]);
                    		Log::info('lover_relation', ['用户'.$user_info['id']."与".$lover_id."建立关系"]);
                    	}
                    }

                    if ($request->session()->get('user_info')) {
                    	return $next($request);
                    }
                    
                    User::whereOpenid($wechat_user['openid'])->update(['last_login' => date('Y-m-d H:i:s', time())]);
                    $request->session()->set('user_info', $user_info->toArray());
                } elseif ($user_info && $user_info['id'] > 0&&$user_info['block']=='2') {
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
            } else {
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
