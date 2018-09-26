<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Wechat;

class HomeController extends Controller {

    public function index() {
        return view('welcome');
    }

    /**
     * 上传视频token
     */
    public function getQiniuQavToken() {
        header('Access-Control-Allow-Origin:*');
        $wmImg = \Qiniu\base64_urlSafeEncode('http://rwxf.qiniudn.com/logo-s.png');
        $pfopOps = "avthumb/m3u8/wmImage/$wmImg";
        $policy = array(
            //'persistentOps' => $pfopOps,
            //'persistentNotifyUrl' => 'http://172.30.251.210:8080/cb.php',
            'persistentPipeline' => '',
        );
        $place = "usercover";
        $token = _qiniu_create_token(null, $policy, $place);
        $domain = _qiniu_get_domain($place);
        return response()->json(['uptoken' => $token]);
    }

    public function test() {
        try {
            $notice = Wechat::staff();
            $result = $notice->send(["touser" => 'ot3XZtyEcBJWjpXJxxyqAcpBCdGY', "msgtype" => "text",
                "text" => ["content" => "亲爱的家人，恭喜您成功开通和会员！国庆活动所赠天数已经直接为您添加，<a href='http://m.hrwq.com/vip/records'>点击此处查看</a>"]]);
        
            dd($result);
        } catch (\Exception $ex) {
            
        }

        
//        require_once(app_path('Library/SmsClient.php'));
//        $client = new \SmsClient(config('sms.gwUrl'), config('sms.serialNumber'), config('sms.password'), config('sms.sessionKey'));
//
//        $res = $client->sendSMS(["13146182306"], "亲爱的家人，恭喜您成功开通和会员！国庆活动所赠天数已经直接为您添加，可进入“和润好父母”公众号查看！");
    }

}
