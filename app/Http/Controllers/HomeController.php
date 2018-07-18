<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }
    
    /**
     * 上传视频token
     */
    public function getQiniuQavToken(){
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
}
