<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CouponUser;
use App\Models\UserPointVip;
use Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ShareController extends Controller {

    public function index() {
        get_score(2);
        return response()->json(['code' => 0, 'message' => '分享成功！']);
    }

    public function loveAngle() {
        $lovebg64 = '';
        $name_color = '';
        $instance = \App\Models\Tooler::getByType(\App\Models\Tooler::TYPE_LOVE_BG);
        $user = user_info();
        if (!empty($instance['content']['base64'])) {
            $lovebg64 = $instance['content']['base64'];
            $name_color = $instance['content']['name_color'];
        } else {
            $lovebg64 = $this->base64EncodeImage(public_path('images/share/love-bg.jpg'));
        }
        $profileIcon = ltrim($user['profileIcon'], '/');
        QrCode::errorCorrection('H');
        $qrcode64 = "data:image/png;base64," . base64_encode(QrCode::format('png')->size(1500)->margin(0)
                                ->merge("/public/" . $profileIcon, .2)
                                ->generate(route('share.hot', ['id' => $user['id']])));
        return view('share.love_angle', ['data' => $user, 'qrcode64' => $qrcode64, 'lovebg64' => $lovebg64, 'name_color' => $name_color]);
    }

    public function hot($id) {
        $lover_id = $id;
        $lover_time = date('Y-m-d H:i:s');
        $user_info = user_info();

        //爱心大使ID不为0，且用户是非会员，不可关联自己
        if ($lover_id != 0 && $user_info['vip_flg'] == 1 && $lover_id != $user_info['id']) {
            //建立或更新关系
            User::whereOpenid($user_info['openid'])->update(['lover_id' => $lover_id, 'lover_time' => $lover_time]);
            Log::info('lover_relation', ['用户' . $user_info['id'] . "与" . $lover_id . "建立关系"]);
        }

        $back = request('back');
        if (!empty($back)) {
            return redirect($back);
        }

        if ($user_info['vip_flg'] == 2) {
            return redirect("/vcourse");
        } else if ($user_info['mobile'] || $user_info['vip_flg'] == 1) {
            return redirect("/article/6");
        } else {
            return view('share.hot', ['user' => $user_info]);
        }
    }

    public function audio() {
        return view('share.hot');
    }

    private function base64EncodeImage($image_file) {
        $base64_image = '';
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        return $base64_image;
    }

    /**
     * 国庆活动页面
     */
    public function active_national_day() {
        return view('share.active.national_day', ['user_info' => user_info()]);
    }

    /**
     * 领取活动优惠券
     */
    public function active_receive_coupon() {
        $user = user_info();
        $user_id = $user['id'];
        $coupon_id = _national_day_coupon_id();
        $result = CouponUser::receiveOne($coupon_id, $user_id);
        return response()->json(['code' => $result->success ? 0 : 1, 'message' => $result->message, 'data' => $result]);
    }
    
    /**
     * 领取vip天数
     */
    public function active_receive_vipday(){
        $user = user_info();
        $user_id = $user['id'];
        $days = 30;
        $vip_point_source = 10;
        
        $oldPointVip = UserPointVip::where(['user_id' => $user_id, 'source' => $vip_point_source])->first();
        if(!empty($oldPointVip)){
            return response()->json(['code' =>  1, 'message' => '已经领取过了', 'data' => $oldPointVip]);
        }
        
        $lover_left_days = get_new_vip_left_day($user['vip_left_day'], $days);
	User::find($user_id)->update(['vip_left_day' => $lover_left_days]);
        UserPointVip::add($user_id, $days, $vip_point_source);
        return response()->json(['code' => 0, 'message' => '领取成功']);
    }
    
}
