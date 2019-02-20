<?php

namespace App\Listeners;

use Wechat;
use Log;

class VipLeftDayEventListener {

    public function subscribe($events) {
        $events->listen('App\Events\VipLeftDay', 'App\Listeners\VipLeftDayEventListener@onTemplate');
    }

    public function onTemplate($event) {
        $left_day = $event->user->left_day;
        $first = "";
//        if ($left_day === 3) {
//            $first = "您的和会员有效期仅剩3天，到期后将无法享受会员权限，请及时开通会员或进行续费。";
//        } else if ($left_day === 1) {
//            $first = "您的和会员有效期仅剩最后1天，到期后将无法享受会员权限，请及时开通会员或进行续费。";
//        } else if ($left_day === 0) {
//            $first = "您的和会员已到期，已无法享受会员权限，请及时开通会员或进行续费。";
//        } else {
//            return; //未开通会员
//        }
        if ($left_day === 1) {
            $first = "您的和会员有效期仅剩最后1天，到期后将无法享受会员权限，请及时开通会员或进行续费。";
        } else {
            return;
        }

        $result = send_sms([$event->user->mobile], $first);
        Log::info($result);
    }

}
