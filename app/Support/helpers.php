<?php

use GuzzleHttp\Psr7\str;

if (!function_exists('is_mobile')) {

    /**
     * 粗略判断是否移动端浏览器
     *
     * @return bool
     */
    function is_mobile() {
        $regex = '/(iPhone|iPod|iPad|Android|BlackBerry|mobile|MicroMessenger)/';
        return preg_match($regex, $_SERVER['HTTP_USER_AGENT']) ? true : false;
    }

}

if (!function_exists('is_wechat')) {

    /**
     * 判断是否微信浏览器
     *
     * @return bool
     */
    function is_wechat() {
        return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
    }

}

if (!function_exists('thumb_uri')) {

    /**
     * 根据原图 uri 获取缩略图 uri
     *
     * @param $uri
     * @param $prefix
     * @return string
     */
    function thumb_uri($uri, $prefix) {
        $uriParts = explode('/', $uri);
        $partsCount = count($uriParts);
        if ($partsCount) {
            $uriParts[$partsCount - 1] = $prefix . $uriParts[$partsCount - 1];
        }
        $thumbUri = implode('/', $uriParts);
        return $thumbUri;
    }

}

if (!function_exists('replace_content_image_url')) {

    /**
     * 替换u-editor里面的图片地址为后台完整地址
     *
     * @param $data   U-editor里面的content
     * @return mixed
     */
    function replace_content_image_url($data) {
        return preg_replace('/(?<=src=)(\"\/)/i', '"' . config('constants.admin_url'), $data);
    }

}

if (!function_exists('get_order_code')) {

    /**
     * @param $type
     * @return string
     *
     * 获取订单号
     */
    function get_order_code($type) {
        $type = intval($type);
        //产品类型  01好课 02好看 03壹家壹 04好问提问 05好问偷听 06和会员
        $type_list = array('1' => '01', '2' => '02', '3' => '03', '4' => '04', '5' => '05', '6' => '06');
        $order_code = $type_list[$type] . date('YmdHis') . rand(1000, 9999);
        return $order_code;
    }

}


if (!function_exists('get_score')) {

    /**
     * 获取积分
     *
     */
    function get_score($type, $money = 0, $user_id = '') {
        if (config('app.debug') === true) {
            return true;
        }

        $type = intval($type);
        $money = intval($money);
        if (empty($user_id)) {
            $user_id = session('user_info')['id'];
        }
        //来源:1注册 2分享 4发帖 5评论 6作业 7笔记 8推荐好友注册  12观看视频 13消费 14线下核销
        $score_list = array('1' => '10', '2' => '10', '4' => '5', '5' => '5', '6' => '5', '7' => '5', '8' => '10', '12' => '5', '13' => '1', '14' => '10');

        // 计算当天已经获得的总的积分
        $score_total = App\Models\UserPoint::where('move_way', 1)
                ->where('created_at', '>=', date('Y-m-d 00:00:00'))
                ->where('created_at', '<=', date('Y-m-d 23:59:59'))
                ->where('user_id', $user_id)
                ->where('source', '<>', 10)//10为取消订单等返还积分  不计在内
                ->sum('point_value');

        // 每天的积分上限是200
        if ($score_total >= 200) {
            return 0;
        } else {
            $score = $score_list[$type];
            if ($type == 13) {
                $score = intval($money / 10);
            }

            $userpoint = new App\Models\UserPoint;
            $userpoint->user_id = $user_id;
            $userpoint->point_value = $score;
            $userpoint->source = $type;
            $userpoint->move_way = 1;

            $user = App\Models\User::find($user_id);
            $user->score += $score;
            $user->grow += $score;

            if ($userpoint->save() && $user->save()) {
                //发送微信提醒
                $scoreSources = config('constants.income_point_source');
                if (isset($scoreSources[$type])) {
                    if (config('app.debug') === false) {
                        try{
                            $notice = \Wechat::notice();
                            $notice->send([
                                'touser' => $user->openid,
                                'template_id' => 'oxk8-ixLvD_XqQ8enFSy1wJ6qrwziLdeHv7KJqybfwE',
                                'url' => route('user.score'),
                                'topcolor' => '#f7f7f7',
                                'data' => [
                                    'first' => '恭喜你获得和贝奖励',
                                    'keyword1' => '+' . $score,
                                    'keyword2' => $scoreSources[$type],
                                    'keyword3' => (string) \Carbon\Carbon::now(),
                                    'keyword4' => $user->score,
                                    'remark' => '和贝可抵扣听课费，点击查看积分详情'
                                ],
                            ]); 
                        }catch(\Exception $e){
                            
                        }
                        
                    }
                }
                return $score;
            } else {
                return 0;
            }
        }
    }

}


if (!function_exists('format_date')) {

    /**
     * @param $time     要格式化的时间戳
     * @return string   返回的字符串表示  如：2天前，3星期前
     */
    function format_date($time) {
        $t = time() - $time;
        $f = array(
            '31536000' => '年',
            '2592000' => '个月',
            '604800' => '星期',
            '86400' => '天',
            '3600' => '小时',
            '60' => '分钟',
            '1' => '秒'
        );
        foreach ($f as $k => $v) {
            if (0 != $c = floor($t / (int) $k)) {
                return $c . $v . '前';
            }
        }
    }

}

if (!function_exists('user_info')) {

    /**
     * 根据session user id
     *
     * @param string $key 获取用户指定地段值 空 或者 用户信息数组
     * @return string
     */
    function user_info($key = '') {
        //if (! session('user_info')) return null;
        if ($key == 'id') {
            return session('user_info')['id'];
        } else {
            $user_info = App\Models\User::find(session('user_info')['id'])->toArray();

            $order = App\Models\Order::where('user_id', $user_info['id'])->where('pay_type', 6)->whereIn('order_type', [2, 4])->first();
            $user_info['finish_order'] = $order;

            return $key ? (isset($user_info[$key]) ? $user_info[$key] : '') : $user_info;
        }
    }

}

if (!function_exists('replace_em')) {

    /**
     * QQ表情插件显示正则替换
     *
     * @param $str
     * @return mixed
     */
    function replace_em($str) {
        $str = preg_replace("/\\[em_([0-9]*)\\]/i", '<img src="' . asset('images/face/$1.gif') . '" border="0" />', $str);
        return $str;
    }

}

if (!function_exists('set_audio_state')) {

    /**
     * 设置音频状态      1一元旁听  2限时免费 3vip免费 4已支付
     *
     * @param $item  问题question  AR
     */
    function set_audio_state($item) {
        $cur_time = time();
        $user_info = user_info();

        //初始化
        $item->audio_type = 1;
        $item->audio_msg = '1元旁听';

        $vip_flg = $user_info['vip_flg'];
        $vip_left_day = computer_vip_left_day($user_info['vip_left_day']);

        //vip免费听 / 指导师本身 免费听
        if ($vip_left_day > 0 || $item->tutor_id == $user_info['id']) {
            $item->audio_type = 3;
            $item->audio_msg = '点击旁听';
        } else {
            if ($item->free_flg == 1 && $cur_time >= strtotime($item->free_from) && $cur_time <= strtotime($item->free_end)) {
                $item->audio_type = 2;
                $item->audio_msg = '限时免费';
            } else {
                //是否支付  todo 已付款状态 2 未付款 2已付款
                $is_paid = App\Models\Order::where('user_id', $user_info['id'])->where('pay_id', $item->id)->whereIn('pay_type', [4, 5])->where('order_type', 2)->first();
                if ($is_paid) {
                    $item->audio_type = 4;
                    $item->audio_msg = '点击旁听';
                }
            }
        }
    }

}


if (!function_exists('get_platform_current_amount')) {

    /**
     * 获取平台当前总金额
     *
     */
    function get_platform_current_amount() {
        return App\Models\Income::orderBy('id', 'desc')->limit(1)->pluck('total_amount');
    }

}

if (!function_exists('admin_url')) {

    /**
     * 根据原图 uri 获取缩略图 uri
     *
     * @param $uri
     * @param $host
     * @return string
     */
    function admin_url($uri, $host = NULL) {
        return $host ? $host : config('constants.admin_url') . $uri;
    }

}

if (!function_exists('qiniu_url')) {

    /**
     * @param $uri
     * @param null $host
     * @return null|string
     *
     *
     */
    function qiniu_url($uri, $host = NULL) {
        return $host ? $host : config('qiniu.DOMAIN') . $uri;
    }

}

if (!function_exists('get_month_days')) {

    /**
     * 根据 年月 获取当月的具体天
     *
     * @param string $year_month
     * @return array
     */
    function get_month_days($year_month = '') {
        $year_month = $year_month ?: date('Y-m');
        $times = strtotime($year_month);

        $day_arr = [];
        $days = date('t', $times);
        for ($i = 1; $i <= $days; $i++) {
            if ($i < 10) {
                $i = '0' . $i;
            }
            $day_arr[] = date('Y-m-', $times) . $i;
        }
        return $day_arr;
    }

}

if (!function_exists('send_sms')) {

    /**
     * @param $mobile
     * @param $content
     * @return int
     *
     * 发送手机短信 返回0表示正常
     */
    function send_sms($mobile, $content) {
        require_once(app_path('Library/SmsClient.php'));
        $client = new \SmsClient(config('sms.gwUrl'), config('sms.serialNumber'), config('sms.password'), config('sms.sessionKey'));
        $res = $client->sendSMS($mobile, $content);
        return $res;
    }

}

if (!function_exists('curl_file_get_contents')) {

    /**
     * @param $durl
     * @return mixed
     */
    function curl_file_get_contents($durl) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $durl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

}

if (!function_exists('qiniu_previews')) {

    /**
     * @param $file
     * @return array|bool
     *
     * 获取七牛文件经过 yifangyun_preview 的预览图
     */
    function qiniu_previews($file) {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $prefix = substr($file, 0, -(strlen($extension) + 1)) . '.';

        $auth = new \Qiniu\Auth(config('qiniu.AK'), config('qiniu.SK'));
        $bucketMgr = new \Qiniu\Storage\BucketManager($auth);

        $bucket = config('qiniu.BUCKET_NAME_FILE');

        $marker = '';
        $limit = 1000;

        list($items, $marker, $err) = $bucketMgr->listFiles($bucket, $prefix, $marker, $limit);
        if ($err !== null) {
            return false;
        } else {
            $fileList = [];
            if (!empty($items)) {
                $count = count($items);
                for ($i = 1; $i <= $count - 1; ++$i) {
                    $fileList[] = $prefix . $i . '.jpg';
                }
            }
            return $fileList;
        }
    }

}

if (!function_exists('computer_vip_left_day')) {

    function computer_vip_left_day($vip_left_day) {
        $left_day = 0;
        if (!empty($vip_left_day)) {
            $d1 = strtotime(date('Y-m-d'));
            $d2 = strtotime($vip_left_day);
            $left_day = round(($d2 - $d1) / 3600 / 24);
        }

        if ($left_day < 0) {
            $left_day = 0;
        }

        if ($left_day > 365 * 10) {
            return '长期';
        }

        return $left_day;
    }
}

/**
 * 获取当前登录用户是否vip
 * @return boolean true是
 */
function get_is_vip (){
    $user = user_info();
    return $user['vip_forever'] == 2 || $user['vip_flg'] == 2 ;
}

/**
 * 是否长期和会员
 * @return boolean true是
 */
function get_is_vip_forever(){
    $user = user_info();
    return $user['vip_forever'] == 2;
}

/**
 * 获取当前登录用户的vip剩余天数
 * @tutorial 用户和会员返回9999999
 * @return int
 */
function get_vip_left_day_number() {
    if (get_is_vip_forever()) {
        return 9999999;
    }
    $left_day = computer_vip_left_day(user_info()['vip_left_day']);
    if(is_string($left_day)){
        return 100000;
    }
    return $left_day;
}

/**
 * 获取当前登录用户的vip剩余天数，长期和会员返回“长期”
 * @return string
 */
function get_vip_left_day_text(){
    if (get_is_vip_forever()) {
        return '长期';
    }
    return computer_vip_left_day(user_info()['vip_left_day']);
}

if (!function_exists('get_new_vip_left_day')) {

    /**
     * 增加天数的和会员有效期
     * @param date $vip_left_day 格式date('Y-m-d')
     * @param int $days 新增天数
     * @return 新的和会员有效期格式如date('Y-m-d')
     */
    function get_new_vip_left_day($vip_left_day, $days) {
        if (!empty($vip_left_day) && strtotime($vip_left_day) < time()) {//已有和会员过期天数置空
            $vip_left_day = null;
        }

        $left_days = 0;
        if (empty($vip_left_day)) {
            $left_days = date('Y-m-d', strtotime("+ {$days} day"));
        } else {
            $left_days = date('Y-m-d', strtotime("+ {$days} day", strtotime($vip_left_day)));
        }
        if ($left_days < 0) {
            $left_days = 0;
        }
        if (computer_vip_left_day($left_days) <= 0) {
            $left_days = date('Y-m-d', strtotime("+ {$days} day"));
        }

        return $left_days;
    }

}


if (!function_exists('diff_two_days')) {

    /**
     * 得到两个日期间的天数
     * @param string $day1 date('Y-m-d H:i:s')
     * @param string $day2 date('Y-m-d H:i:s')
     * @return number 相差天数
     */
    function diff_tow_days($day1, $day2) {
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);

        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return intval(($second1 - $second2) / 86400);
    }

}

/**
 * 获取直播地址
 * @return Ambigous <string, multitype:>
 */
function _get_telecast_link() {
    if (preg_match('/^win/i', PHP_OS)) {
        $data = file_get_contents('E:/sug_link.log');
    } else {
        $data = file_get_contents('/mnt/sug_link.log');
    }

    if (!empty($data)) {
        list($telecast, $foreshow) = explode("\n", $data);
    } else {
        $telecast = '';
        $foreshow = '';
    }

    return $telecast;
}

function _qiniu_get_buket($place = "usercover") {
    if ($place == "usercover") {
        $buket = config('qiniu.BUCKET_NAME_USERCOVER');
        if (config('app.env') === 'dev') {
            $buket = config('qiniu.BUCKET_NAME_DEVELOP');
        }
        return $buket;
    } else {
        throw new Exception('未定义空间_qiniu_get_buket');
    }
}

/**
 * 
 * @param type $place
 * @return string
 * @throws Exception
 */
function _qiniu_get_domain($place = "usercover") {
    if ($place == "usercover") {
        $domain = config('qiniu.DOMAIN_USERCOVER');
        if (config('app.env') === 'dev') {
            $domain = config('qiniu.DOMAIN_DEVELOP');
        }
        return rtrim($domain, '/') . '/';
    } else {
        throw new Exception('未定义域名_qiniu_get_domain');
    }
}

/**
 * 生成七牛token
 * @param type $key 新文件名或null
 * @param type $policy
 * @param type $place usercover
 * @return string
 */
function _qiniu_create_token($key, $policy = array(), $place = "usercover") {
    $ak = config('qiniu.AK');
    $sk = config('qiniu.SK');
    $buket = _qiniu_get_buket($place);

    $auth = new \Qiniu\Auth($ak, $sk);
    $token = $auth->uploadToken($buket, $key, 3600, $policy);
    return $token;
}

/**
 * 七牛原图上传不压缩
 * @param type $filepath
 * @param type $qu_dir
 * @param type $oldName
 * @param type $useOldName
 * @return type
 */
function _qiniu_upload_img($filepath, $qu_dir, $oldName = null, $useOldName = false, $place = "usercover") {
    $uuid = $oldName;
    if ($useOldName === false) {
        $extistion = empty($oldName) ? 'jpg' : substr(strrchr($oldName, '.'), 1);
        $uuid = str_replace('.', '', uniqid("", TRUE)) . "." . $extistion;
    }
    $newName = $qu_dir . '/' . $uuid;

    $token = _qiniu_create_token(null, null);
    // 初始化 UploadManager 对象并进行文件的上传。
    $uploadMgr = new \Qiniu\Storage\UploadManager ();
    // 调用 UploadManager 的 putFile 方法进行文件的上传。
    list ( $ret, $err ) = $uploadMgr->putFile($token, $newName, $filepath);

    $url = _qiniu_get_domain($place) . $newName;
    return array('url' => $url, 'ret' => $ret, 'err' => $err);
}

/**
 * 生成缩略图后上传
 * @param type $filepath
 * @param type $qu_dir
 * @param type $oldName
 * @param type $useOldName
 * @return type
 */
function _qiniu_upload_img_thumb($filepath, $qu_dir, $oldName = null, $useOldName = false, $place = "usercover") {
    $uuid = $oldName;
    if ($useOldName === false) {
        $extistion = empty($oldName) ? 'jpg' : substr(strrchr($oldName, '.'), 1);
        $uuid = str_replace('.', '', uniqid("", TRUE)) . "-thumb." . $extistion;
    }
    $newName = $qu_dir . '/' . $uuid;

    $buket = _qiniu_get_buket($place);
    #$key = $newName;
    # 设置图片缩略参数
    //https://developer.qiniu.com/dora/manual/1279/basic-processing-images-imageview2
    $fops = 'imageView2/4/w/200/h/200'; //宽最少200，等比剪裁
    //生成EncodedEntryURI的值
    $entry = "{$buket}:{$newName}"; //<Key>为生成缩略图的文件名
    //生成的值
    $saveas_key = \Qiniu\base64_urlSafeEncode($entry);

    $fops = $fops . '|saveas/' . $saveas_key;
    # 在上传策略中指定fobs和pipeline
    $policy = array(
        'persistentOps' => $fops,
            //'persistentPipeline' => ""
    );

    $token = _qiniu_create_token($newName, $policy);
    // 初始化 UploadManager 对象并进行文件的上传。
    $uploadMgr = new \Qiniu\Storage\UploadManager ();
    // 调用 UploadManager 的 putFile 方法进行文件的上传。
    list ( $ret, $err ) = $uploadMgr->putFile($token, $newName, $filepath);
    $url = _qiniu_get_domain($place) . '/' . $newName;
    return array('url' => $url, 'ret' => $ret, 'err' => $err);
}

/**
 * 验证可否看到合伙人卡片
 * @param type $isAbort
 * @return boolean
 */
function _validateCard($isAbort = true) {
    $userInfo = user_info();
    
    if ($userInfo['role'] != 3 
            && empty(\App\Models\UserPartnerWhites::where('user_id', $userInfo['id'])->select('user_id')->get()->toArray())) {
        if ($isAbort === true) {
            abort(403, '此功能仅对百万家庭幸福工程合伙人开放。');
        }
        return false;
    }
    return true;
}

/**
 * 判断是否在支付白名单中
 * @param string $openidOrUid
 * @return boolean
 */
function _in_paywhitelist($openidOrUid){
    $whitelist = ['ot3XZtyEcBJWjpXJxxyqAcpBCdGY','ot3XZt41_M-OX9ihvC0_w05DU68Q'];
    $idWhitelist = [4];
    if(is_numeric($openidOrUid)){
        return in_array($openidOrUid, $idWhitelist);
    }
    return in_array($openidOrUid, $whitelist);
}

/**
 * 是否在节日期间
 * @return type
 */
function _is_festival(){
    return strtotime("2018-11-11 23:59:59") > time() && time() > strtotime("2018-11-10 07:30:00");
}

function _festival_replace($old, $new){
    if(_is_festival()){
        return $new;
    }
    return $old;
}