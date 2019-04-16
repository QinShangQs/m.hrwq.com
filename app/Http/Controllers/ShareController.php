<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
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
        QrCode::errorCorrection('H');
        $qrcode64 = "data:image/png;base64," . base64_encode(QrCode::format('png')->size(1500)->margin(0)
                                ->merge("/public".$user['profileIcon'], .2)
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

}
